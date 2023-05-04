<?php

namespace Dreamlabs\GraphQL\Parser\Ast\Interfaces;


interface ValueInterface
{

    public function getValue();

    public function setValue($value);
}
