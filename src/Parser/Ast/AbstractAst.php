<?php
/**
 * Date: 16.11.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Parser\Ast;


use Youshido\GraphQL\Parser\Ast\Interfaces\LocatableInterface;
use Youshido\GraphQL\Parser\Location;

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