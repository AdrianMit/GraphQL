<?php

namespace Dreamlabs\GraphQL\Config\Object;

use Dreamlabs\GraphQL\Config\AbstractConfig;
use Dreamlabs\GraphQL\Config\Traits\FieldsAwareConfigTrait;
use Dreamlabs\GraphQL\Config\TypeConfigInterface;
use Dreamlabs\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Dreamlabs\GraphQL\Type\TypeService;

/**
 * Class ObjectTypeConfig
 * @package Dreamlabs\GraphQL\Config\Object
 * @method setDescription(string $description)
 * @method string getDescription()
 */
class ObjectTypeConfig extends AbstractConfig implements TypeConfigInterface
{
    use FieldsAwareConfigTrait;

    public function getRules(): array
    {
        return [
            'name'        => ['type' => TypeService::TYPE_STRING, 'required' => true],
            'description' => ['type' => TypeService::TYPE_STRING],
            'fields'      => ['type' => TypeService::TYPE_ARRAY_OF_FIELDS_CONFIG, 'final' => true],
            'interfaces'  => ['type' => TypeService::TYPE_ARRAY_OF_INTERFACES]
        ];
    }

    protected function build(): void
    {
        $this->buildFields();
    }

    public function getInterfaces(): array
    {
        return $this->get('interfaces', []);
    }

}
