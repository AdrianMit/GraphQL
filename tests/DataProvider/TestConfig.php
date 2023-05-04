<?php


namespace Dreamlabs\Tests\DataProvider;


use Dreamlabs\GraphQL\Config\AbstractConfig;
use Dreamlabs\GraphQL\Type\TypeService;

class TestConfig extends AbstractConfig
{
    public function getRules()
    {
        return [
            'name'    => ['type' => TypeService::TYPE_ANY, 'required' => true],
            'resolve' => ['type' => TypeService::TYPE_CALLABLE, 'final' => true],
        ];
    }

}
