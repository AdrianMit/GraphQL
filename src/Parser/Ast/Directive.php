<?php

namespace Dreamlabs\GraphQL\Parser\Ast;


use Dreamlabs\GraphQL\Parser\Location;

class Directive extends AbstractAst
{
    use AstArgumentsTrait;


    /**
     * @param string   $name
     * @param array    $arguments
     * @param Location $location
     */
    public function __construct(private $name, array $arguments, Location $location)
    {
        parent::__construct($location);
        $this->setArguments($arguments);
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

}
