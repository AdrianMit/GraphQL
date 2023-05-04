<?php

namespace Dreamlabs\GraphQL\Config\Object;


use Dreamlabs\GraphQL\Type\TypeService;

class ListTypeConfig extends ObjectTypeConfig
{
    public function getRules(): array
    {
        return [
            'itemType' => ['type' => TypeService::TYPE_GRAPHQL_TYPE, 'final' => true],
            'resolve'  => ['type' => TypeService::TYPE_CALLABLE],
            'args'     => ['type' => TypeService::TYPE_ARRAY_OF_INPUT_FIELDS],
        ];
    }

}
