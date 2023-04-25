<?php
/**
 * Date: 16.11.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Exception\Parser;


use Exception;
use Youshido\GraphQL\Exception\Interfaces\LocationableExceptionInterface;
use Youshido\GraphQL\Parser\Location;

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
