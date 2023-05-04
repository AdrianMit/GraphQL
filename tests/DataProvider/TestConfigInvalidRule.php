<?php

namespace Dreamlabs\Tests\DataProvider;


use Dreamlabs\GraphQL\Config\AbstractConfig;
use Dreamlabs\GraphQL\Type\TypeService;

class TestConfigInvalidRule extends AbstractConfig
{
    public function getRules()
    {
        return [
            'name'             => ['type' => TypeService::TYPE_ANY, 'required' => true],
            'invalidRuleField' => ['type' => TypeService::TYPE_ANY, 'invalid rule' => true]
        ];
    }

}
