<?php

namespace Dreamlabs\GraphQL\Config\Field;

use Dreamlabs\GraphQL\Config\AbstractConfig;
use Dreamlabs\GraphQL\Config\Traits\ArgumentsAwareConfigTrait;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\TypeInterface;
use Dreamlabs\GraphQL\Type\TypeService;

/**
 * Class FieldConfig
 * @package Dreamlabs\GraphQL\Config\Field
 * @method $this setDescription(string $description)
 * @method $this setCost(int $cost)
 */
class FieldConfig extends AbstractConfig
{
    use ArgumentsAwareConfigTrait;

    public function getRules(): array
    {
        return [
            'name'              => ['type' => TypeService::TYPE_STRING, 'final' => true],
            'type'              => ['type' => TypeService::TYPE_GRAPHQL_TYPE, 'final' => true],
            'args'              => ['type' => TypeService::TYPE_ARRAY],
            'description'       => ['type' => TypeService::TYPE_STRING],
            'resolve'           => ['type' => TypeService::TYPE_CALLABLE],
            'isDeprecated'      => ['type' => TypeService::TYPE_BOOLEAN],
            'deprecationReason' => ['type' => TypeService::TYPE_STRING],
            'cost'              => ['type' => TypeService::TYPE_ANY]
        ];
    }

    protected function build(): void
    {
        $this->buildArguments();
    }

    public function getType(): TypeInterface|AbstractObjectType
    {
        return $this->data['type'];
    }

}
