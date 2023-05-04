<?php

namespace Dreamlabs\GraphQL\Exception\Interfaces;


use Dreamlabs\GraphQL\Parser\Location;

interface LocationableExceptionInterface
{
    public function getLocation(): Location;

}
