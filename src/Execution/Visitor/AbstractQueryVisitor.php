<?php

namespace Dreamlabs\GraphQL\Execution\Visitor;

use Dreamlabs\GraphQL\Config\Field\FieldConfig;

abstract class AbstractQueryVisitor
{

    /**
     * @var int initial value of $this->memo
     */
    protected int $initialValue = 0;

    /**
     * @var mixed the accumulator
     */
    protected int $memo;

    /**
     * AbstractQueryVisitor constructor.
     */
    public function __construct()
    {
        $this->memo = $this->initialValue;
    }

    /**
     * @return mixed getter for the memo, in case callers want to inspect it after a process run
     */
    public function getMemo(): mixed
    {
        return $this->memo;
    }

    /**
     * Visit a query node.  See class docstring.
     *
     * @param array       $args
     * @param FieldConfig $fieldConfig
     * @param int         $childScore
     *
     * @return int|null
     */
    abstract public function visit(array $args, FieldConfig $fieldConfig, int $childScore = 0): ?int;
}
