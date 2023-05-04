<?php

namespace Dreamlabs\Tests\DataProvider;


use Dreamlabs\GraphQL\Type\Enum\AbstractEnumType;

class TestEnumType extends AbstractEnumType
{
    public function getValues()
    {
        return [
            [
                'name'  => 'FINISHED',
                'value' => 1,
            ],
            [
                'name'  => 'NEW',
                'value' => 0,
            ]
        ];
    }

}
