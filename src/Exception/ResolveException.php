<?php

namespace Dreamlabs\GraphQL\Exception;


use Exception;
use Dreamlabs\GraphQL\Exception\Interfaces\LocationableExceptionInterface;
use Dreamlabs\GraphQL\Parser\Location;

class ResolveException extends Exception implements LocationableExceptionInterface
{

    public function __construct(string $message, private ?Location $location = null)
    {
        parent::__construct($message);
    }

    public function getLocation(): Location
    {
        return $this->location;
    }
}
