<?php

namespace Dreamlabs\Tests\DataProvider;


use Dreamlabs\GraphQL\Type\Union\AbstractUnionType;

class TestUnionType extends AbstractUnionType
{

    public function getTypes()
    {
        return [
            new TestObjectType()
        ];
    }

    public function resolveType($object)
    {
        return $object;
    }

    public function getDescription(): string
    {
        return 'Union collect cars types';
    }


}
