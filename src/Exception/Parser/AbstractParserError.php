<?php

namespace Dreamlabs\GraphQL\Exception\Parser;


use Exception;
use Dreamlabs\GraphQL\Exception\Interfaces\LocationableExceptionInterface;
use Dreamlabs\GraphQL\Parser\Location;

abstract class AbstractParserError extends Exception implements LocationableExceptionInterface
{

    public function __construct(string $message, private Location $location)
    {
        parent::__construct($message);
    }

    public function getLocation(): Location
    {
        return $this->location;
    }
}
