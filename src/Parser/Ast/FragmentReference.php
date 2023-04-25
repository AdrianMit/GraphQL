<?php
/**
 * Date: 23.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Parser\Ast;


use Youshido\GraphQL\Parser\Ast\Interfaces\FragmentInterface;
use Youshido\GraphQL\Parser\Location;

class FragmentReference extends AbstractAst implements FragmentInterface
{

    /**
     * @param string   $name
     * @param Location $location
     */
    public function __construct(protected $name, Location $location)
    {
        parent::__construct($location);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }


}