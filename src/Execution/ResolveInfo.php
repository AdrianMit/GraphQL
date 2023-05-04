<?php

namespace Dreamlabs\GraphQL\Execution;

use Dreamlabs\GraphQL\Execution\Container\ContainerInterface;
use Dreamlabs\GraphQL\Execution\Context\ExecutionContextInterface;
use Dreamlabs\GraphQL\Field\FieldInterface;
use Dreamlabs\GraphQL\Parser\Ast\Field;
use Dreamlabs\GraphQL\Parser\Ast\Query;
use Dreamlabs\GraphQL\Type\AbstractType;

class ResolveInfo
{
    protected mixed $container;

    /**
     * @param Field[] $fieldASTList
     */
    public function __construct(protected FieldInterface $field, protected array $fieldASTList, protected ExecutionContextInterface $executionContext)
    {
    }

    public function getExecutionContext(): ExecutionContextInterface
    {
        return $this->executionContext;
    }

    public function getField(): ?FieldInterface
    {
        return $this->field;
    }

    public function getFieldAST(string $fieldName): null|Query|Field
    {
        $field = null;
        foreach ($this->getFieldASTList() as $fieldAST) {
            if ($fieldAST->getName() === $fieldName) {
                $field = $fieldAST;
                break;
            }
        }

        return $field;
    }

    /**
     * @return Field[]
     */
    public function getFieldASTList(): array
    {
        return $this->fieldASTList;
    }

    /**
     * @return AbstractType
     */
    public function getReturnType(): AbstractType
    {
        return $this->field->getType();
    }

    public function getContainer(): ContainerInterface
    {
        return $this->executionContext->getContainer();
    }


}
