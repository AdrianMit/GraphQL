<?php
/**
 * Date: 16.11.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Exception\Interfaces;


use Youshido\GraphQL\Parser\Location;

interface LocationableExceptionInterface
{
    public function getLocation(): Location;

}
