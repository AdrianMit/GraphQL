<?php

namespace Dreamlabs\GraphQL\Config\Field;


use Dreamlabs\GraphQL\Config\AbstractConfig;
use Dreamlabs\GraphQL\Type\TypeService;

/**
 * Class InputFieldConfig
 * @package Dreamlabs\GraphQL\Config\Field
 * @method $this setDescription(string $description)
 */
class InputFieldConfig extends AbstractConfig
{

    public function getRules(): array
    {
        return [
            'name'              => ['type' => TypeService::TYPE_STRING, 'final' => true],
            'type'              => ['type' => TypeService::TYPE_ANY_INPUT, 'final' => true],
            'defaultValue'      => ['type' => TypeService::TYPE_ANY],
            'description'       => ['type' => TypeService::TYPE_STRING],
            'isDeprecated'      => ['type' => TypeService::TYPE_BOOLEAN],
            'deprecationReason' => ['type' => TypeService::TYPE_STRING],
        ];
    }

    public function getDefaultValue(): mixed
    {
        return $this->get('defaultValue');
    }

}
