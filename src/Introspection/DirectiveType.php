<?php

namespace Dreamlabs\GraphQL\Introspection;

use Dreamlabs\GraphQL\Config\Directive\DirectiveConfig;
use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;
use Dreamlabs\GraphQL\Directive\Directive;
use Dreamlabs\GraphQL\Directive\DirectiveInterface;
use Dreamlabs\GraphQL\Exception\ConfigurationException;
use Dreamlabs\GraphQL\Type\ListType\ListType;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\TypeMap;

class DirectiveType extends AbstractObjectType
{

    /**
     * @return String type name
     */
    public function getName(): string
    {
        return '__Directive';
    }

    public function resolveArgs(DirectiveInterface $value): array
    {
        if ($value->hasArguments()) {
            return $value->getArguments();
        }

        return [];
    }
    
    public function resolveLocations(DirectiveInterface|Directive $value): array
    {
        /** @var DirectiveConfig $directiveConfig */
        $directiveConfig = $value->getConfig();

        $locations = $directiveConfig->getLocations();

        return $locations;
    }
    
    /**
     * @throws ConfigurationException
     */
    public function build(ObjectTypeConfig $config): void
    {
        $config
            ->addField('name', new NonNullType(TypeMap::TYPE_STRING))
            ->addField('description', TypeMap::TYPE_STRING)
            ->addField('args', [
                'type'    => new NonNullType(new ListType(new NonNullType(new InputValueType()))),
                'resolve' => [$this, 'resolveArgs'],
            ])
            ->addField('locations',[
                'type'  =>  new NonNullType(new ListType(new NonNullType(new DirectiveLocationType()))),
                'resolve' => [$this, 'resolveLocations'],
            ]);
    }
}
