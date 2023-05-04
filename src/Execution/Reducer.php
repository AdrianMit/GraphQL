<?php

namespace Dreamlabs\GraphQL\Execution;


use Dreamlabs\GraphQL\Execution\Context\ExecutionContextInterface;
use Dreamlabs\GraphQL\Execution\Visitor\AbstractQueryVisitor;
use Dreamlabs\GraphQL\Field\Field;
use Dreamlabs\GraphQL\Field\FieldInterface;
use Dreamlabs\GraphQL\Parser\Ast\Field as FieldAst;
use Dreamlabs\GraphQL\Parser\Ast\FragmentReference;
use Dreamlabs\GraphQL\Parser\Ast\Interfaces\FragmentInterface;
use Dreamlabs\GraphQL\Parser\Ast\Mutation;
use Dreamlabs\GraphQL\Parser\Ast\Query;
use Dreamlabs\GraphQL\Type\AbstractType;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\Union\AbstractUnionType;

class Reducer
{

    private ?ExecutionContextInterface $executionContext = null;

    /**
     * Apply all of $reducers to this query.  Example reducer operations: checking for maximum query complexity,
     * performing look-ahead query planning, etc.
     *
     * @param AbstractQueryVisitor[]    $reducers
     */
    public function reduceQuery(ExecutionContextInterface $executionContext, array $reducers): void
    {
        $this->executionContext = $executionContext;
        $schema                 = $executionContext->getSchema();

        foreach ($reducers as $reducer) {
            foreach ($executionContext->getRequest()->getAllOperations() as $operation) {
                $this->doVisit($operation, $operation instanceof Mutation ? $schema->getMutationType() : $schema->getQueryType(), $reducer);
            }
        }
    }

    protected function doVisit(Query $query, AbstractType $currentLevelSchema, AbstractQueryVisitor $reducer): void
    {
        if (!($currentLevelSchema instanceof AbstractObjectType) || !$currentLevelSchema->hasField($query->getName())) {
            return;
        }

        if ($operationField = $currentLevelSchema->getField($query->getName())) {

            $coroutine = $this->walkQuery($query, $operationField);

            if ($results = $coroutine->current()) {
                $queryCost = 0;
                while ($results) {
                    // initial values come from advancing the generator via ->current, subsequent values come from ->send()
                    [$queryField, $astField, $childCost] = $results;

                    /**
                     * @var Query|FieldAst $queryField
                     * @var Field          $astField
                     */
                    $cost = $reducer->visit($queryField->getKeyValueArguments(), $astField->getConfig(), $childCost);
                    $queryCost += $cost;
                    $results = $coroutine->send($cost);
                }
            }
        }
    }
    
    protected function walkQuery(Query|Field|FragmentInterface $queryNode, FieldInterface $currentLevelAST): \Generator
    {
        $childrenScore = 0;
        if (!($queryNode instanceof FieldAst)) {
            foreach ($queryNode->getFields() as $queryField) {
                if ($queryField instanceof FragmentInterface) {
                    if ($queryField instanceof FragmentReference) {
                        $queryField = $this->executionContext->getRequest()->getFragment($queryField->getName());
                    }
                    // the next 7 lines are essentially equivalent to `yield from $this->walkQuery(...)` in PHP7.
                    // for backwards compatibility this is equivalent.
                    // This pattern is repeated multiple times in this function, and unfortunately cannot be extracted or
                    // made less verbose.
                    $gen  = $this->walkQuery($queryField, $currentLevelAST);
                    $next = $gen->current();
                    while ($next) {
                        $received = (yield $next);
                        $childrenScore += (int)$received;
                        $next = $gen->send($received);
                    }
                } else {
                    $fieldType = $currentLevelAST->getType()->getNamedType();
                    if ($fieldType instanceof AbstractUnionType) {
                        foreach ($fieldType->getTypes() as $unionFieldType) {
                            if ($fieldAst = $unionFieldType->getField($queryField->getName())) {
                                $gen  = $this->walkQuery($queryField, $fieldAst);
                                $next = $gen->current();
                                while ($next) {
                                    $received = (yield $next);
                                    $childrenScore += (int)$received;
                                    $next = $gen->send($received);
                                }
                            }
                        }
                    } elseif ($fieldType instanceof AbstractObjectType && $fieldAst = $fieldType->getField($queryField->getName())) {
                        $gen  = $this->walkQuery($queryField, $fieldAst);
                        $next = $gen->current();
                        while ($next) {
                            $received = (yield $next);
                            $childrenScore += (int)$received;
                            $next = $gen->send($received);
                        }
                    }
                }
            }
        }
        // sanity check.  don't yield fragments; they don't contribute to cost
        if ($queryNode instanceof Query || $queryNode instanceof FieldAst) {
            // BASE CASE.  If we're here we're done recursing -
            // this node is either a field, or a query that we've finished recursing into.
            yield [$queryNode, $currentLevelAST, $childrenScore];
        }
    }

}
