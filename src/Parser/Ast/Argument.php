<?php
/**
 * Date: 23.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Parser\Ast;


use Youshido\GraphQL\Parser\Ast\Interfaces\ValueInterface;
use Youshido\GraphQL\Parser\Location;

class Argument extends AbstractAst
{

    /**
     * @param string         $name
     * @param ValueInterface $value
     * @param Location       $location
     */
    public function __construct(private $name, private ValueInterface $value, Location $location)
    {
        parent::__construct($location);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    public function setName(mixed $name): void
    {
        $this->name = $name;
    }

    /**
     * @return \Youshido\GraphQL\Parser\Ast\Interfaces\ValueInterface
     */
    public function getValue()
    {
        return $this->value;
    }

    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }


}