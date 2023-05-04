<?php

namespace Dreamlabs\Tests\DataProvider;


use Dreamlabs\GraphQL\Config\AbstractConfig;
use Dreamlabs\GraphQL\Type\TypeService;

class TestConfigExtraFields extends AbstractConfig
{

    protected ?bool $extraFieldsAllowed = true;

    public function getRules()
    {
        return [
            'name' => ['type' => TypeService::TYPE_ANY, 'required' => true]
        ];
    }


}
