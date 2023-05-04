<?php

namespace Dreamlabs\GraphQL\Parser\Ast;


use Dreamlabs\GraphQL\Parser\Ast\Interfaces\FragmentInterface;
use Dreamlabs\GraphQL\Parser\Location;

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
