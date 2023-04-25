<?php
/*
* Concrete implementation of query visitor.
*
* Enforces maximum complexity on a query, computed from "cost" functions on
* the fields touched by that query.
*
* @author Ben Roberts <bjr.roberts@gmail.com>
* created: 7/11/16 11:05 AM
*/

namespace Youshido\GraphQL\Execution\Visitor;


use Exception;
use Youshido\GraphQL\Config\Field\FieldConfig;

class MaxComplexityQueryVisitor extends AbstractQueryVisitor
{

    /**
     * @var int default score for nodes without explicit cost functions
     */
    protected int $defaultScore = 1;

    /**
     * MaxComplexityQueryVisitor constructor.
     *
     * @param int $maxScore max allowed complexity score
     */
    public function __construct(public int $maxScore)
    {
        parent::__construct();
    }
    
    /**
     * @throws Exception
     */
    public function visit(array $args, FieldConfig $fieldConfig, int $childScore = 0): ?int
    {
        $cost = $fieldConfig->get('cost', null);
        if (is_callable($cost)) {
            $cost = $cost($args, $fieldConfig, $childScore);
        }

        $cost = is_null($cost) ? $this->defaultScore : $cost;
        $this->memo += $cost;

        if ($this->memo > $this->maxScore) {
            throw new Exception('query exceeded max allowed complexity of ' . $this->maxScore);
        }

        return $cost;
    }
}
