<?php

namespace Dreamlabs\GraphQL\Parser\Ast\ArgumentValue;


use Dreamlabs\GraphQL\Parser\Ast\AbstractAst;
use Dreamlabs\GraphQL\Parser\Ast\Interfaces\ValueInterface;
use Dreamlabs\GraphQL\Parser\Location;

class InputObject extends AbstractAst implements ValueInterface
{

    /**
     * @param array    $object
     * @param Location $location
     */
    public function __construct(protected array $object, Location $location)
    {
        parent::__construct($location);
    }

    public function getValue(): array
    {
        return $this->object;
    }

    /**
     * @param array $value
     */
    public function setValue($value): void
    {
        $this->object = $value;
    }

}
