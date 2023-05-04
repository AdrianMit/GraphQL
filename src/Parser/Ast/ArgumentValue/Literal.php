<?php

namespace Dreamlabs\GraphQL\Parser\Ast\ArgumentValue;


use Dreamlabs\GraphQL\Parser\Ast\AbstractAst;
use Dreamlabs\GraphQL\Parser\Ast\Interfaces\ValueInterface;
use Dreamlabs\GraphQL\Parser\Location;

class Literal extends AbstractAst implements ValueInterface
{

    /**
     * @param mixed $value
     * @param Location $location
     */
    public function __construct(private $value, Location $location)
    {
        parent::__construct($location);
    }

    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }
}
