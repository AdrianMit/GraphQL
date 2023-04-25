<?php
/**
 * Date: 26.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Exception;


use Exception;
use Youshido\GraphQL\Exception\Interfaces\LocationableExceptionInterface;
use Youshido\GraphQL\Parser\Location;

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
