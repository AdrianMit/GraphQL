<?php

namespace Dreamlabs\GraphQL\Introspection;

use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;
use Dreamlabs\GraphQL\Exception\ConfigurationException;
use Dreamlabs\GraphQL\Field\Field;
use Dreamlabs\GraphQL\Schema\AbstractSchema;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\TypeInterface;
use Dreamlabs\GraphQL\Type\TypeMap;
use JsonException;

class InputValueType extends AbstractObjectType
{

    public function resolveType(AbstractSchema|Field $value): TypeInterface
    {
        return $value->getConfig()->getType();
    }
    
    /**
     * @throws JsonException
     */
    public function resolveDefaultValue(AbstractSchema|Field $value): ?string
    {
        $resolvedValue = $value->getConfig()->getDefaultValue();
        return $resolvedValue === null ? $resolvedValue : str_replace('"', '', json_encode($resolvedValue, JSON_THROW_ON_ERROR));
    }
    
    /**
     * @throws ConfigurationException
     */
    public function build(ObjectTypeConfig $config): void
    {
        $config
            ->addField('name', new NonNullType(TypeMap::TYPE_STRING))
            ->addField('description', TypeMap::TYPE_STRING)
            ->addField('isDeprecated', new NonNullType(TypeMap::TYPE_BOOLEAN))
            ->addField('deprecationReason', TypeMap::TYPE_STRING)
            ->addField(new Field([
                'name'    => 'type',
                'type'    => new NonNullType(new QueryType()),
                'resolve' => [$this, 'resolveType']
            ]))
            ->addField('defaultValue', [
                'type' => TypeMap::TYPE_STRING,
                'resolve' => [$this, 'resolveDefaultValue']
            ]);
    }

    /**
     * @return string type name
     */
    public function getName(): string
    {
        return '__InputValue';
    }
}
