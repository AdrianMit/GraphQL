<?php

namespace Dreamlabs\GraphQL\Introspection;

use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;
use Dreamlabs\GraphQL\Exception\ConfigurationException;
use Dreamlabs\GraphQL\Field\Field;
use Dreamlabs\GraphQL\Introspection\Field\TypesField;
use Dreamlabs\GraphQL\Schema\AbstractSchema;
use Dreamlabs\GraphQL\Type\ListType\ListType;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;

class SchemaType extends AbstractObjectType
{

    /**
     * @return String type name
     */
    public function getName(): string
    {
        return '__Schema';
    }

    public function resolveQueryType($value)
    {
        /** @var AbstractSchema|Field $value */
        return $value->getQueryType();
    }

    public function resolveMutationType($value)
    {
        /** @var AbstractSchema|Field $value */
        return $value->getMutationType()->hasFields() ? $value->getMutationType() : null;
    }

    public function resolveSubscriptionType()
    {
        return null;
    }

    public function resolveDirectives($value)
    {
        /** @var AbstractSchema|Field $value */
        $dirs = $value->getDirectiveList()->getDirectives();
        return $dirs;
    }
    
    /**
     * @throws ConfigurationException
     */
    public function build(ObjectTypeConfig $config): void
    {
        $config
            ->addField(new Field([
                'name'    => 'queryType',
                'type'    => new QueryType(),
                'resolve' => [$this, 'resolveQueryType']
            ]))
            ->addField(new Field([
                'name'    => 'mutationType',
                'type'    => new QueryType(),
                'resolve' => [$this, 'resolveMutationType']
            ]))
            ->addField(new Field([
                'name'    => 'subscriptionType',
                'type'    => new QueryType(),
                'resolve' => [$this, 'resolveSubscriptionType']
            ]))
            ->addField(new TypesField())
            ->addField(new Field([
                'name'    => 'directives',
                'type'    => new ListType(new DirectiveType()),
                'resolve' => [$this, 'resolveDirectives']
            ]));
    }
}
