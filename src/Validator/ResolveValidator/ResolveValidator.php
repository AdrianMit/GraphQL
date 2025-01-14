<?php

namespace Dreamlabs\GraphQL\Validator\ResolveValidator;


use Dreamlabs\GraphQL\Exception\ResolveException;
use Dreamlabs\GraphQL\Execution\Context\ExecutionContext;
use Dreamlabs\GraphQL\Execution\Request;
use Dreamlabs\GraphQL\Field\FieldInterface;
use Dreamlabs\GraphQL\Field\InputField;
use Dreamlabs\GraphQL\Parser\Ast\Interfaces\FieldInterface as AstFieldInterface;
use Dreamlabs\GraphQL\Type\AbstractType;
use Dreamlabs\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\TypeMap;
use Dreamlabs\GraphQL\Type\TypeService;
use Dreamlabs\GraphQL\Type\Union\AbstractUnionType;

class ResolveValidator implements ResolveValidatorInterface
{

    /**
     * ResolveValidator constructor.
     */
    public function __construct(private ExecutionContext $executionContext)
    {
    }
    
    public function assetTypeHasField(AbstractType $objectType, AstFieldInterface $ast)
    {
        /** @var AbstractObjectType $objectType */
        if ($this->executionContext->getField($objectType, $ast->getName()) !== null) {
            return;
        }
        
        if (!(TypeService::isObjectType($objectType) || TypeService::isInputObjectType($objectType)) || !$objectType->hasField($ast->getName())) {
            $availableFieldNames = implode(', ', array_map(fn(FieldInterface $field): string => sprintf('"%s"', $field->getName()), $objectType->getFields()));
            throw new ResolveException(sprintf('Field "%s" not found in type "%s". Available fields are: %s', $ast->getName(), $objectType->getNamedType()->getName(), $availableFieldNames), $ast->getLocation());
        }
    }

    public function assertValidArguments(FieldInterface $field, AstFieldInterface $query, Request $request)
    {
        $requiredArguments = array_filter($field->getArguments(), fn(InputField $argument): bool => $argument->getType()->getKind() === TypeMap::KIND_NON_NULL);

        foreach ($query->getArguments() as $astArgument) {
            if (!$field->hasArgument($astArgument->getName())) {
                throw new ResolveException(sprintf('Unknown argument "%s" on field "%s"', $astArgument->getName(), $field->getName()), $astArgument->getLocation());
            }

            $argument     = $field->getArgument($astArgument->getName());
            $argumentType = $argument->getType()->getNullableType();

            switch ($argumentType->getKind()) {
                case TypeMap::KIND_ENUM:
                case TypeMap::KIND_SCALAR:
                case TypeMap::KIND_INPUT_OBJECT:
                case TypeMap::KIND_LIST:
                    if (!$argument->getType()->isValidValue($astArgument->getValue())) {
                        $error = $argument->getType()->getValidationError($astArgument->getValue()) ?: '(no details available)';
                        throw new ResolveException(sprintf('Not valid type for argument "%s" in query "%s": %s', $astArgument->getName(), $field->getName(), $error), $astArgument->getLocation());
                    }

                    break;

                default:
                    throw new ResolveException(sprintf('Invalid argument type "%s"', $argumentType->getName()));
            }

            if (array_key_exists($astArgument->getName(), $requiredArguments) || $argument->getConfig()->get('defaultValue') !== null) {
                unset($requiredArguments[$astArgument->getName()]);
            }
        }

        if (count($requiredArguments)) {
            throw new ResolveException(sprintf('Require "%s" arguments to query "%s"', implode(', ', array_keys($requiredArguments)), $query->getName()));
        }
    }

    public function assertValidResolvedValueForField(FieldInterface $field, $resolvedValue)
    {
        if (null === $resolvedValue && $field->getType()->getKind() === TypeMap::KIND_NON_NULL) {
            throw new ResolveException(sprintf('Cannot return null for non-nullable field "%s"', $field->getName()));
        }

        $nullableFieldType = $field->getType()->getNullableType();
        if (!$nullableFieldType->isValidValue($resolvedValue)) {
            $error = $nullableFieldType->getValidationError($resolvedValue) ?: '(no details available)';
            throw new ResolveException(sprintf('Not valid resolved type for field "%s": %s', $field->getName(),
                $error));
        }
    }

    public function assertTypeImplementsInterface(AbstractType $type, AbstractInterfaceType $interface)
    {
        if ($type instanceof AbstractObjectType) {
            foreach ($type->getInterfaces() as $typeInterface) {
                if ($typeInterface->getName() === $interface->getName()) {
                    return;
                }
            }
        }

        throw new ResolveException(sprintf('Type "%s" does not implement "%s"', $type->getName(), $interface->getName()));
    }

    public function assertTypeInUnionTypes(AbstractType $type, AbstractUnionType $unionType)
    {
        foreach ($unionType->getTypes() as $unionTypeItem) {
            if ($unionTypeItem->getName() === $type->getName()) {
                return;
            }
        }

        throw new ResolveException(sprintf('Type "%s" not exist in types of "%s"', $type->getName(), $unionType->getName()));
    }
}
