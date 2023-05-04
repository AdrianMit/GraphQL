<?php

namespace Dreamlabs\GraphQL\Config\Object;


use Dreamlabs\GraphQL\Type\TypeService;

class InputObjectTypeConfig extends ObjectTypeConfig
{
    public function getRules(): array
    {
        return [
            'name'        => ['type' => TypeService::TYPE_STRING, 'required' => true],
            'fields'      => ['type' => TypeService::TYPE_ARRAY_OF_INPUT_FIELDS, 'final' => true],
            'description' => ['type' => TypeService::TYPE_STRING],
        ];
    }
}
