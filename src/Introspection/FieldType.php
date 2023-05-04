<?php

namespace Dreamlabs\GraphQL\Introspection;

use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;
use Dreamlabs\GraphQL\Exception\ConfigurationException;
use Dreamlabs\GraphQL\Field\FieldInterface;
use Dreamlabs\GraphQL\Type\AbstractType;
use Dreamlabs\GraphQL\Type\ListType\ListType;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\TypeMap;

class FieldType extends AbstractObjectType
{

    public function resolveType(FieldInterface $value): AbstractType
    {
        return $value->getType();
    }

    public function resolveArgs(FieldInterface $value): array
    {
        if ($value->hasArguments()) {
            return $value->getArguments();
        }

        return [];
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
            ->addField('type', [
                'type'    => new NonNullType(new QueryType()),
                'resolve' => [$this, 'resolveType'],
            ])
            ->addField('args', [
                'type'    => new NonNullType(new ListType(new NonNullType(new InputValueType()))),
                'resolve' => [$this, 'resolveArgs'],
            ]);
    }

    public function isValidValue(mixed $value): bool
    {
        return $value instanceof FieldInterface;
    }

    /**
     * @return String type name
     */
    public function getName(): string
    {
        return '__Field';
    }
}
