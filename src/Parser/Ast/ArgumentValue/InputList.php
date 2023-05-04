<?php

namespace Dreamlabs\GraphQL\Parser\Ast\ArgumentValue;


use Dreamlabs\GraphQL\Parser\Ast\AbstractAst;
use Dreamlabs\GraphQL\Parser\Ast\Interfaces\ValueInterface;
use Dreamlabs\GraphQL\Parser\Location;

class InputList extends AbstractAst implements ValueInterface
{

    /**
     * @param array    $list
     * @param Location $location
     */
    public function __construct(protected array $list, Location $location)
    {
        parent::__construct($location);
    }

    public function getValue(): array
    {
        return $this->list;
    }

    /**
     * @param array $value
     */
    public function setValue($value): void
    {
        $this->list = $value;
    }
}
