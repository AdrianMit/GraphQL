<?php

namespace Dreamlabs\GraphQL\Type;


interface AbstractInterfaceTypeInterface
{
    /**
     * @param $object object from resolve function
     *
     * @return AbstractType
     */
    public function resolveType($object);
}
