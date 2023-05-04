<?php

namespace Dreamlabs\GraphQL\Type;

interface CompositeTypeInterface
{

    /**
     * @return AbstractType
     */
    public function getTypeOf();
}
