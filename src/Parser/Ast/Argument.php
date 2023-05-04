<?php

namespace Dreamlabs\GraphQL\Parser\Ast;


use Dreamlabs\GraphQL\Parser\Ast\Interfaces\ValueInterface;
use Dreamlabs\GraphQL\Parser\Location;

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

    public function getValue(): ValueInterface
    {
        return $this->value;
    }

    public function setValue(ValueInterface $value): void
    {
        $this->value = $value;
    }
}
