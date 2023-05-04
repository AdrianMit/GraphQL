<?php

namespace Dreamlabs\GraphQL\Config\Object;


use Dreamlabs\GraphQL\Config\AbstractConfig;
use Dreamlabs\GraphQL\Config\Traits\ArgumentsAwareConfigTrait;
use Dreamlabs\GraphQL\Config\Traits\FieldsAwareConfigTrait;
use Dreamlabs\GraphQL\Config\TypeConfigInterface;
use Dreamlabs\GraphQL\Type\TypeService;

class EnumTypeConfig extends AbstractConfig implements TypeConfigInterface
{
    use FieldsAwareConfigTrait, ArgumentsAwareConfigTrait;

    public function getRules(): array
    {
        return [
            'name'        => ['type' => TypeService::TYPE_STRING, 'final' => true],
            'description' => ['type' => TypeService::TYPE_STRING],
            'values'      => ['type' => TypeService::TYPE_ENUM_VALUES, 'required' => true],
        ];
    }

    public function getValues(): mixed
    {
        return $this->get('values', []);
    }

}
