<?php

namespace Dreamlabs\GraphQL\Execution;


use Dreamlabs\GraphQL\Exception\ConfigurationException;
use Dreamlabs\GraphQL\Exception\Parser\InvalidRequestException;
use Dreamlabs\GraphQL\Exception\Parser\SyntaxErrorException;
use Dreamlabs\GraphQL\Exception\ResolveException;
use Dreamlabs\GraphQL\Execution\Container\Container;
use Dreamlabs\GraphQL\Execution\Context\ExecutionContext;
use Dreamlabs\GraphQL\Execution\Visitor\MaxComplexityQueryVisitor;
use Dreamlabs\GraphQL\Field\Field;
use Dreamlabs\GraphQL\Field\FieldInterface;
use Dreamlabs\GraphQL\Field\InputField;
use Dreamlabs\GraphQL\Parser\Ast\ArgumentValue\InputList as AstInputList;
use Dreamlabs\GraphQL\Parser\Ast\ArgumentValue\InputObject as AstInputObject;
use Dreamlabs\GraphQL\Parser\Ast\ArgumentValue\Literal as AstLiteral;
use Dreamlabs\GraphQL\Parser\Ast\ArgumentValue\VariableReference;
use Dreamlabs\GraphQL\Parser\Ast\Field as AstField;
use Dreamlabs\GraphQL\Parser\Ast\Fragment;
use Dreamlabs\GraphQL\Parser\Ast\FragmentReference;
use Dreamlabs\GraphQL\Parser\Ast\Interfaces\FieldInterface as AstFieldInterface;
use Dreamlabs\GraphQL\Parser\Ast\Mutation as AstMutation;
use Dreamlabs\GraphQL\Parser\Ast\Query as AstQuery;
use Dreamlabs\GraphQL\Parser\Ast\TypedFragmentReference;
use Dreamlabs\GraphQL\Parser\Parser;
use Dreamlabs\GraphQL\Schema\AbstractSchema;
use Dreamlabs\GraphQL\Type\AbstractType;
use Dreamlabs\GraphQL\Type\Enum\AbstractEnumType;
use Dreamlabs\GraphQL\Type\InputObject\AbstractInputObjectType;
use Dreamlabs\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Dreamlabs\GraphQL\Type\ListType\AbstractListType;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\Scalar\AbstractScalarType;
use Dreamlabs\GraphQL\Type\TypeMap;
use Dreamlabs\GraphQL\Type\Union\AbstractUnionType;
use Dreamlabs\GraphQL\Validator\RequestValidator\RequestValidator;
use Dreamlabs\GraphQL\Validator\ResolveValidator\ResolveValidator;
use Dreamlabs\GraphQL\Validator\ResolveValidator\ResolveValidatorInterface;
use Exception;
use InvalidArgumentException;

class Processor
{
    
    public const TYPE_NAME_QUERY = '__typename';
    protected ExecutionContext $executionContext;
    protected ResolveValidatorInterface $resolveValidator;
    protected array $data;
    protected ?int $maxComplexity = null;
    protected array $deferredResultsLeaf = [];
    protected array $deferredResultsComplex = [];
    
    public function __construct(AbstractSchema $schema)
    {
        if (empty($this->executionContext)) {
            $this->executionContext = new ExecutionContext($schema);
            $this->executionContext->setContainer(new Container());
        }
        
        $this->resolveValidator = new ResolveValidator($this->executionContext);
    }
    
    public function processPayload(mixed $payload, array $variables = [], array $reducers = []): static
    {
        $this->data = [];
        
        try {
            $this->parseAndCreateRequest($payload, $variables);
            
            if ($this->maxComplexity) {
                $reducers[] = new MaxComplexityQueryVisitor($this->maxComplexity);
            }
            
            if ($reducers) {
                $reducer = new Reducer();
                $reducer->reduceQuery($this->executionContext, $reducers);
            }
            
            // Resolve all queries/operations and merge their data at the end
            $operationResults = [];
            
            foreach ($this->executionContext->getRequest()->getAllOperations() as $query) {
                if ($operationResult = $this->resolveQuery($query)) {
                    $operationResults[] = $operationResult;
                }
            }
            
            $this->data = $this->combineResults($operationResults);
            
            // If the processor found any deferred results, resolve them now.
            if ( ! empty($this->data) && ( ! empty($this->deferredResultsLeaf) || ! empty($this->deferredResultsComplex))) {
                try {
                    while ($deferredResolver = array_shift($this->deferredResultsComplex)) {
                        $deferredResolver->resolve();
                    }
                    
                    // Deferred scalar and enum fields should be resolved last to
                    // pick up as many as possible for a single batch.
                    while ($deferredResolver = array_shift($this->deferredResultsLeaf)) {
                        $deferredResolver->resolve();
                    }
                } catch (Exception $e) {
                    $this->executionContext->addError($e);
                } finally {
                    $this->data = static::unpackDeferredResults($this->data);
                }
            }
        } catch (Exception $e) {
            $this->executionContext->addError($e);
        }
        
        return $this;
    }
    
    /**
     * Unpack results stored inside deferred resolvers.
     *
     * @param mixed $result
     *   The result ree.
     *
     *   The unpacked result.
     */
    public static function unpackDeferredResults(mixed $result): mixed
    {
        while ($result instanceof DeferredResult) {
            $result = $result->result;
        }
        
        if (is_array($result)) {
            foreach ($result as $key => $value) {
                $result[$key] = static::unpackDeferredResults($value);
            }
        }
        
        return $result;
    }
    
    /**
     * @throws ResolveException|ConfigurationException
     */
    protected function resolveQuery(AstQuery $query): array
    {
        $schema = $this->executionContext->getSchema();
        $type   = $query instanceof AstMutation ? $schema->getMutationType() : $schema->getQueryType();
        $field  = new Field([
            'name' => $query instanceof AstMutation ? 'mutation' : 'query',
            'type' => $type,
        ]);
        
        if (self::TYPE_NAME_QUERY == $query->getName()) {
            return [$this->getAlias($query) => $type->getName()];
        }
        
        $this->resolveValidator->assetTypeHasField($type, $query);
        $value = $this->resolveField($field, $query);
        
        return [$this->getAlias($query) => $value];
    }
    
    /**
     * @throws ResolveException
     */
    protected function resolveField(
        FieldInterface $field,
        AstFieldInterface $ast,
        $parentValue = null,
        $fromObject = false
    ) {
        try {
            /** @var AbstractObjectType $type */
            $type        = $field->getType();
            $nonNullType = $type->getNullableType();
            
            if (self::TYPE_NAME_QUERY == $ast->getName()) {
                return $nonNullType->getName();
            }
            
            $this->resolveValidator->assetTypeHasField($nonNullType, $ast);
            
            $targetField = $this->executionContext->getField($nonNullType, $ast->getName());
            
            $this->prepareAstArguments($targetField, $ast, $this->executionContext->getRequest());
            $this->resolveValidator->assertValidArguments($targetField, $ast, $this->executionContext->getRequest());
            
            switch ($kind = $targetField->getType()->getNullableType()->getKind()) {
                case TypeMap::KIND_ENUM:
                case TypeMap::KIND_SCALAR:
                    if ($ast instanceof AstQuery && $ast->hasFields()) {
                        throw new ResolveException(
                            sprintf(
                                'You can\'t specify fields for scalar type "%s"',
                                $targetField->getType()->getNullableType()->getName()
                            ), $ast->getLocation()
                        );
                    }
                    
                    return $this->resolveScalar($targetField, $ast, $parentValue);
                
                case TypeMap::KIND_OBJECT:
                    /** @var $type AbstractObjectType */
                    if ( ! $ast instanceof AstQuery) {
                        throw new ResolveException(
                            sprintf('You have to specify fields for "%s"', $ast->getName()),
                            $ast->getLocation()
                        );
                    }
                    
                    return $this->resolveObject($targetField, $ast, $parentValue);
                
                case TypeMap::KIND_LIST:
                    return $this->resolveList($targetField, $ast, $parentValue);
                
                case TypeMap::KIND_UNION:
                case TypeMap::KIND_INTERFACE:
                    if ( ! $ast instanceof AstQuery) {
                        throw new ResolveException(
                            sprintf('You have to specify fields for "%s"', $ast->getName()),
                            $ast->getLocation()
                        );
                    }
                    
                    return $this->resolveComposite($targetField, $ast, $parentValue);
                
                default:
                    throw new ResolveException(sprintf('Resolving type with kind "%s" not supported', $kind));
            }
        } catch (Exception $e) {
            $this->executionContext->addError($e);
            
            if ($fromObject) {
                throw $e;
            }
            
            return null;
        }
    }
    
    /**
     * @throws ResolveException
     */
    private function prepareAstArguments(FieldInterface $field, AstFieldInterface $query, Request $request): void
    {
        foreach ($query->getArguments() as $astArgument) {
            if ($field->hasArgument($astArgument->getName())) {
                $argumentType = $field->getArgument($astArgument->getName())->getType()->getNullableType();

                $astArgument->setValue($this->prepareArgumentValue($astArgument->getValue(), $argumentType, $request));
            }
        }
    }
    
    /**
     * @throws ResolveException
     */
    private function prepareArgumentValue($argumentValue, AbstractType $argumentType, Request $request)
    {
        switch ($argumentType->getKind()) {
            case TypeMap::KIND_LIST:
                /** @var $argumentType AbstractListType */
                $result = [];
                if ($argumentValue instanceof AstInputList || is_array($argumentValue)) {
                    $list = is_array($argumentValue) ? $argumentValue : $argumentValue->getValue();
                    foreach ($list as $item) {
                        $result[] = $this->prepareArgumentValue(
                            $item,
                            $argumentType->getItemType()->getNullableType(),
                            $request
                        );
                    }
                } else {
                    if ($argumentValue instanceof VariableReference) {
                        return $this->getVariableReferenceArgumentValue($argumentValue, $argumentType, $request);
                    }
                }
                
                return $result;
            
            case TypeMap::KIND_INPUT_OBJECT:
                /** @var $argumentType AbstractInputObjectType */
                $result = [];
                if ($argumentValue instanceof AstInputObject) {
                    foreach ($argumentType->getFields() as $field) {
                        /** @var $field Field */
                        if ($field->getConfig()->has('defaultValue')) {
                            $result[$field->getName()] = $field->getType()->getNullableType()->parseInputValue(
                                $field->getConfig()->get('defaultValue')
                            );
                        }
                    }
                    foreach ($argumentValue->getValue() as $key => $item) {
                        if ($argumentType->hasField($key)) {
                            $result[$key] = $this->prepareArgumentValue(
                                $item,
                                $argumentType->getField($key)->getType()->getNullableType(),
                                $request
                            );
                        } else {
                            $result[$key] = $item;
                        }
                    }
                } else {
                    if ($argumentValue instanceof VariableReference) {
                        return $this->getVariableReferenceArgumentValue($argumentValue, $argumentType, $request);
                    } else {
                        if (is_array($argumentValue)) {
                            return $argumentValue;
                        }
                    }
                }
                return $result;
            
            case TypeMap::KIND_SCALAR:
            case TypeMap::KIND_ENUM:
                /** @var $argumentValue AstLiteral|VariableReference */
                if ($argumentValue instanceof VariableReference) {
                    return $this->getVariableReferenceArgumentValue($argumentValue, $argumentType, $request);
                } else {
                    if ($argumentValue instanceof AstLiteral) {
                        return $argumentValue->getValue();
                    } else {
                        return $argumentValue;
                    }
                }
        }
        
        throw new ResolveException('Argument type not supported');
    }
    
    /**
     * @throws ResolveException
     */
    private function getVariableReferenceArgumentValue(
        VariableReference $variableReference,
        AbstractType $argumentType,
        Request $request
    ) {
        $variable = $variableReference->getVariable();
        if ($argumentType->getKind() === TypeMap::KIND_LIST) {
            if (
                ( ! $variable->isArray() && ! is_array($variable->getValue())) ||
                ($variable->getTypeName() !== $argumentType->getNamedType()->getNullableType()->getName()) ||
                ($argumentType->getNamedType()->getKind(
                    ) === TypeMap::KIND_NON_NULL && $variable->isArrayElementNullable())
            ) {
                throw new ResolveException(
                    sprintf(
                        'Invalid variable "%s" type, allowed type is "%s"',
                        $variable->getName(),
                        $argumentType->getNamedType()->getNullableType()->getName()
                    ), $variable->getLocation()
                );
            }
        } else {
            if ($variable->getTypeName() !== $argumentType->getName()) {
                throw new ResolveException(
                    sprintf(
                        'Invalid variable "%s" type, allowed type is "%s"',
                        $variable->getName(),
                        $argumentType->getName()
                    ), $variable->getLocation()
                );
            }
        }
        $requestValue = $request->getVariable($variable->getName());
        if ((null === $requestValue && $variable->isNullable()) && ! $request->hasVariable($variable->getName())) {
            throw new ResolveException(
                sprintf('Variable "%s" does not exist in request', $variable->getName()),
                $variable->getLocation()
            );
        }
        
        return $requestValue;
    }
    
    
    /**
     * @throws ResolveException
     */
    private function collectResult(
        FieldInterface $field,
        AbstractObjectType $type,
        TypedFragmentReference|Fragment|null|AstFieldInterface $ast,
        $resolvedValue
    ): array {
        $results = [];
        
        foreach ($ast->getFields() as $astField) {
            switch (true) {
                case $astField instanceof TypedFragmentReference:
                    $astName  = $astField->getTypeName();
                    $typeName = $type->getName();
                    
                    if ($typeName !== $astName) {
                        foreach ($type->getInterfaces() as $interface) {
                            if ($interface->getName() === $astName) {
                                $results[] = $this->collectResult($field, $type, $astField, $resolvedValue);
                                
                                break;
                            }
                        }
                        
                        continue 2;
                    }
                    
                    $results[] = $this->collectResult($field, $type, $astField, $resolvedValue);
                    
                    break;
                
                case $astField instanceof FragmentReference:
                    $astFragment      = $this->executionContext->getRequest()->getFragment($astField->getName());
                    $astFragmentModel = $astFragment->getModel();
                    $typeName         = $type->getName();
                    
                    if ($typeName !== $astFragmentModel) {
                        foreach ($type->getInterfaces() as $interface) {
                            if ($interface->getName() === $astFragmentModel) {
                                $results[] = $this->collectResult($field, $type, $astFragment, $resolvedValue);
                                
                                break;
                            }
                        }
                        
                        continue 2;
                    }
                    
                    $results[] = $this->collectResult($field, $type, $astFragment, $resolvedValue);
                    
                    break;
                
                default:
                    $results[] = [
                        $this->getAlias($astField) => $this->resolveField(
                            $field,
                            $astField,
                            $resolvedValue,
                            true
                        ),
                    ];
            }
        }
        
        return $this->combineResults($results);
    }
    
    protected function deferredResolve($resolvedValue, FieldInterface $field, callable $callback): DeferredResult
    {
        if ($resolvedValue instanceof DeferredResolverInterface) {
            $deferredResult = new DeferredResult(
                $resolvedValue,
                fn($resolvedValue): DeferredResult => // Allow nested deferred resolvers.
                $this->deferredResolve($resolvedValue, $field, $callback)
            );
            
            // Whenever we stumble upon a deferred resolver, add it to the queue
            // to be resolved later.
            $type = $field->getType()->getNamedType();
            if ($type instanceof AbstractScalarType || $type instanceof AbstractEnumType) {
                $this->deferredResultsLeaf[] = $deferredResult;
            } else {
                $this->deferredResultsComplex[] = $deferredResult;
            }
            
            return $deferredResult;
        }
        
        // For simple values, invoke the callback immediately.
        return $callback($resolvedValue);
    }
    
    /**
     * @throws ResolveException
     */
    protected function resolveScalar(FieldInterface $field, AstFieldInterface $ast, $parentValue): DeferredResult
    {
        $resolvedValue = $this->doResolve($field, $ast, $parentValue);
        
        return $this->deferredResolve($resolvedValue, $field, function ($resolvedValue) use ($field) {
            $this->resolveValidator->assertValidResolvedValueForField($field, $resolvedValue);
            
            /** @var AbstractScalarType $type */
            $type = $field->getType()->getNullableType();
            
            return $type->serialize($resolvedValue);
        });
    }
    
    /**
     * @throws ResolveException|ConfigurationException
     */
    protected function resolveList(FieldInterface $field, AstFieldInterface $ast, $parentValue): DeferredResult
    {
        /** @var AstQuery $ast */
        $resolvedValue = $this->doResolve($field, $ast, $parentValue);
        
        return $this->deferredResolve($resolvedValue, $field, function ($resolvedValue) use ($field, $ast) {
            $this->resolveValidator->assertValidResolvedValueForField($field, $resolvedValue);
            
            if (null === $resolvedValue) {
                return null;
            }
            
            /** @var AbstractListType $type */
            $type     = $field->getType()->getNullableType();
            $itemType = $type->getNamedType();
            
            $fakeAst = clone $ast;
            if ($fakeAst instanceof AstQuery) {
                $fakeAst->setArguments([]);
            }
            
            $fakeField = new Field([
                'name' => $field->getName(),
                'type' => $itemType,
                'args' => $field->getArguments(),
            ]);
            
            $result = [];
            foreach ($resolvedValue as $resolvedValueItem) {
                try {
                    $fakeField->getConfig()->set('resolve', fn() => $resolvedValueItem);
                    
                    $value = match ($itemType->getNullableType()->getKind()) {
                        TypeMap::KIND_ENUM, TypeMap::KIND_SCALAR => $this->resolveScalar(
                            $fakeField,
                            $fakeAst,
                            $resolvedValueItem
                        ),
                        TypeMap::KIND_OBJECT => $this->resolveObject($fakeField, $fakeAst, $resolvedValueItem),
                        TypeMap::KIND_UNION, TypeMap::KIND_INTERFACE => $this->resolveComposite(
                            $fakeField,
                            $fakeAst,
                            $resolvedValueItem
                        ),
                        default => null,
                    };
                } catch (Exception $e) {
                    $this->executionContext->addError($e);
                    
                    $value = null;
                }
                
                $result[] = $value;
            }
            
            return $result;
        });
    }
    
    /**
     * @throws ResolveException
     */
    protected function resolveObject(
        FieldInterface $field,
        AstFieldInterface $ast,
        $parentValue,
        $fromUnion = false
    ): DeferredResult {
        $resolvedValue = $parentValue;
        if ( ! $fromUnion) {
            $resolvedValue = $this->doResolve($field, $ast, $parentValue);
        }
        
        return $this->deferredResolve($resolvedValue, $field, function ($resolvedValue) use ($field, $ast) {
            $this->resolveValidator->assertValidResolvedValueForField($field, $resolvedValue);
            
            if (null === $resolvedValue) {
                return null;
            }
            /** @var AbstractObjectType $type */
            $type = $field->getType()->getNullableType();
            
            try {
                return $this->collectResult($field, $type, $ast, $resolvedValue);
            } catch (Exception) {
                return null;
            }
        });
    }
    
    /**
     * @throws ResolveException|ConfigurationException
     */
    protected function resolveComposite(FieldInterface $field, AstFieldInterface $ast, $parentValue): DeferredResult
    {
        /** @var AstQuery $ast */
        $resolvedValue = $this->doResolve($field, $ast, $parentValue);
        
        return $this->deferredResolve($resolvedValue, $field, function ($resolvedValue) use ($field, $ast) {
            $this->resolveValidator->assertValidResolvedValueForField($field, $resolvedValue);
            
            if (null === $resolvedValue) {
                return null;
            }
            
            /** @var AbstractUnionType $type */
            $type         = $field->getType()->getNullableType();
            $resolveInfo  = new ResolveInfo(
                $field,
                $ast instanceof AstQuery ? $ast->getFields() : [],
                $this->executionContext
            );
            $resolvedType = $type->resolveType($resolvedValue, $resolveInfo);
            
            if ( ! $resolvedType) {
                throw new ResolveException('Resolving function must return type');
            }
            
            if ($type instanceof AbstractInterfaceType) {
                $this->resolveValidator->assertTypeImplementsInterface($resolvedType, $type);
            } else {
                $this->resolveValidator->assertTypeInUnionTypes($resolvedType, $type);
            }
            
            $fakeField = new Field([
                'name' => $field->getName(),
                'type' => $resolvedType,
                'args' => $field->getArguments(),
            ]);
            
            return $this->resolveObject($fakeField, $ast, $resolvedValue, true);
        });
    }
    
    /**
     * @throws SyntaxErrorException
     * @throws InvalidRequestException
     */
    protected function parseAndCreateRequest($payload, $variables = []): void
    {
        if (empty($payload)) {
            throw new InvalidArgumentException('Must provide an operation.');
        }
        
        $parser  = new Parser();
        $request = new Request($parser->parse($payload), $variables);
        
        (new RequestValidator())->validate($request);
        
        $this->executionContext->setRequest($request);
    }
    
    protected function doResolve(FieldInterface $field, AstFieldInterface $ast, $parentValue = null)
    {
        /** @var AstQuery|AstField $ast */
        $arguments = $this->parseArgumentsValues($field, $ast);
        $astFields = $ast instanceof AstQuery ? $ast->getFields() : [];
        
        return $field->resolve($parentValue, $arguments, $this->createResolveInfo($field, $astFields));
    }
    
    protected function parseArgumentsValues(FieldInterface $field, AstFieldInterface $ast): array
    {
        $values   = [];
        $defaults = [];
        
        foreach ($field->getArguments() as $argument) {
            /** @var $argument InputField */
            if ($argument->getConfig()->has('defaultValue')) {
                $defaults[$argument->getName()] = $argument->getConfig()->getDefaultValue();
            }
        }
        
        foreach ($ast->getArguments() as $astArgument) {
            $argument     = $field->getArgument($astArgument->getName());
            $argumentType = $argument->getType()->getNullableType();
            
            $values[$argument->getName()] = $argumentType->parseValue($astArgument->getValue());
            
            if (array_key_exists($argument->getName(), $defaults)) {
                unset($defaults[$argument->getName()]);
            }
        }
        
        return array_merge($values, $defaults);
    }
    
    private function getAlias(AstFieldInterface $ast): string
    {
        return $ast->getAlias() ?: $ast->getName();
    }
    
    protected function createResolveInfo(FieldInterface $field, array $astFields): ResolveInfo
    {
        return new ResolveInfo($field, $astFields, $this->executionContext);
    }
    
    /**
     * Combines the specified results using array_replace_recursive, including graceful handling for empty arrays
     *
     */
    protected function combineResults(array $results): array
    {
        if (count($results) > 0) {
            return call_user_func_array('array_replace_recursive', $results);
        }
        
        return [];
    }
    
    /**
     * You can access ExecutionContext to check errors and inject dependencies
     */
    public function getExecutionContext(): ExecutionContext
    {
        return $this->executionContext;
    }
    
    public function getResponseData(): array
    {
        $result = [];
        
        if ( ! empty($this->data)) {
            $result['data'] = $this->data;
        }
        
        if ($this->executionContext->hasErrors()) {
            $result['errors'] = $this->executionContext->getErrorsArray();
        }
        
        return $result;
    }
    
    public function getMaxComplexity(): int
    {
        return $this->maxComplexity;
    }
    
    public function setMaxComplexity(int $maxComplexity): void
    {
        $this->maxComplexity = $maxComplexity;
    }
    
}
