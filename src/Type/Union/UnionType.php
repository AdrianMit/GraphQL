<?php

namespace Dreamlabs\GraphQL\Type\Union;

final class UnionType extends AbstractUnionType
{

    protected bool $isFinal = true;

    public function resolveType($object)
    {
        $callable = $this->getConfigValue('resolveType');

        return $callable($object);
    }

    public function getTypes()
    {
        return $this->getConfig()->get('types', []);
    }

}
