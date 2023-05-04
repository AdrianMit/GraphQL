<?php

namespace Dreamlabs\GraphQL\Parser\Ast;


use Dreamlabs\GraphQL\Parser\Ast\Interfaces\LocatableInterface;
use Dreamlabs\GraphQL\Parser\Location;

abstract class AbstractAst implements LocatableInterface
{

    public function __construct(private Location $location)
    {
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function setLocation(Location $location): void
    {
        $this->location = $location;
    }
}
