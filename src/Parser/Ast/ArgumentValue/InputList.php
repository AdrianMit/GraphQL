<?php
/**
 * Date: 01.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Parser\Ast\ArgumentValue;


use Youshido\GraphQL\Parser\Ast\AbstractAst;
use Youshido\GraphQL\Parser\Ast\Interfaces\ValueInterface;
use Youshido\GraphQL\Parser\Location;

class InputList extends AbstractAst implements ValueInterface
{

    /**
     * @param array    $list
     * @param Location $location
     */
    public function __construct(protected array $list, Location $location)
    {
        parent::__construct($location);
    }

    /**
     * @return array
     */
    public function getValue()
    {
        return $this->list;
    }

    /**
     * @param array $value
     */
    public function setValue($value): void
    {
        $this->list = $value;
    }
}