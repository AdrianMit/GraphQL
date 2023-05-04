<?php

namespace Dreamlabs\GraphQL\Parser\Ast\Interfaces;


use Dreamlabs\GraphQL\Parser\Location;

interface LocatableInterface
{

    /**
     * @return Location
     */
    public function getLocation();

}
