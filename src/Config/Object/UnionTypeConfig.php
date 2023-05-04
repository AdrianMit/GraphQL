<?php

namespace Dreamlabs\GraphQL\Config\Object;


use Dreamlabs\GraphQL\Config\AbstractConfig;
use Dreamlabs\GraphQL\Config\Traits\ArgumentsAwareConfigTrait;
use Dreamlabs\GraphQL\Config\Traits\FieldsAwareConfigTrait;
use Dreamlabs\GraphQL\Config\TypeConfigInterface;
use Dreamlabs\GraphQL\Type\TypeService;

class UnionTypeConfig extends AbstractConfig implements TypeConfigInterface
{
    use FieldsAwareConfigTrait, ArgumentsAwareConfigTrait;

    public function getRules(): array
    {
        return [
            'name'        => ['type' => TypeService::TYPE_STRING, 'required' => true],
            'types'       => ['type' => TypeService::TYPE_ARRAY_OF_OBJECT_TYPES],
            'description' => ['type' => TypeService::TYPE_STRING],
            'resolveType' => ['type' => TypeService::TYPE_CALLABLE, 'final' => true]
        ];
    }
}
