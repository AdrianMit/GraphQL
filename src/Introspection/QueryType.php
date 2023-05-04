<?php

namespace Dreamlabs\GraphQL\Introspection;

use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;
use Dreamlabs\GraphQL\Exception\ConfigurationException;
use Dreamlabs\GraphQL\Execution\ResolveInfo;
use Dreamlabs\GraphQL\Field\Field;
use Dreamlabs\GraphQL\Introspection\Traits\TypeCollectorTrait;
use Dreamlabs\GraphQL\Type\AbstractType;
use Dreamlabs\GraphQL\Type\CompositeTypeInterface;
use Dreamlabs\GraphQL\Type\Enum\AbstractEnumType;
use Dreamlabs\GraphQL\Type\InputObject\AbstractInputObjectType;
use Dreamlabs\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Dreamlabs\GraphQL\Type\ListType\ListType;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\Scalar\BooleanType;
use Dreamlabs\GraphQL\Type\TypeMap;
use Dreamlabs\GraphQL\Type\Union\AbstractUnionType;

class QueryType extends AbstractObjectType
{

    use TypeCollectorTrait;

    /**
     * @return String type name
     */
    public function getName(): string
    {
        return '__Type';
    }

    public function resolveOfType(AbstractType $value)
    {
        if ($value instanceof CompositeTypeInterface) {
            return $value->getTypeOf();
        }

        return null;
    }

    public function resolveInputFields($value): ?array
    {
        if ($value instanceof AbstractInputObjectType) {
            /** @var AbstractObjectType $value */
            return $value->getConfig()->getFields();
        }

        return null;
    }

    public function resolveEnumValues($value, $args): ?array
    {
        /** @var $value AbstractType|AbstractEnumType */
        if ($value && $value->getKind() == TypeMap::KIND_ENUM) {
            $data = [];
            foreach ($value->getValues() as $enumValue) {
                if(!$args['includeDeprecated'] && (isset($enumValue['isDeprecated']) && $enumValue['isDeprecated'])) {
                    continue;
                }

                if (!array_key_exists('description', $enumValue)) {
                    $enumValue['description'] = '';
                }
                if (!array_key_exists('isDeprecated', $enumValue)) {
                    $enumValue['isDeprecated'] = false;
                }
                if (!array_key_exists('deprecationReason', $enumValue)) {
                    $enumValue['deprecationReason'] = null;
                }

                $data[] = $enumValue;
            }

            return $data;
        }

        return null;
    }

    public function resolveFields($value, $args): ?array
    {
        /** @var AbstractType $value */
        if (!$value ||
            in_array($value->getKind(), [TypeMap::KIND_SCALAR, TypeMap::KIND_UNION, TypeMap::KIND_INPUT_OBJECT, TypeMap::KIND_ENUM])
        ) {
            return null;
        }

        /** @var AbstractObjectType $value */
        return array_filter($value->getConfig()->getFields(), function ($field) use ($args) {
            /** @var $field Field */
            if (in_array($field->getName(), ['__type', '__schema']) || (!$args['includeDeprecated'] && $field->isDeprecated())) {
                return false;
            }

            return true;
        });
    }

    public function resolveInterfaces($value): ?array
    {
        /** @var $value AbstractType */
        if ($value->getKind() == TypeMap::KIND_OBJECT) {
            /** @var $value AbstractObjectType */
            return $value->getConfig()->getInterfaces() ?: [];
        }

        return null;
    }

    public function resolvePossibleTypes($value, $args, ResolveInfo $info): ?array
    {
        /** @var $value AbstractObjectType */
        if ($value->getKind() == TypeMap::KIND_INTERFACE) {
            $schema = $info->getExecutionContext()->getSchema();
            $this->collectTypes($schema->getQueryType());
            foreach ($schema->getTypesList()->getTypes() as $type) {
              $this->collectTypes($type);
            }

            $possibleTypes = [];
            foreach ($this->types as $type) {
                /** @var $type AbstractObjectType */
                if ($type->getKind() == TypeMap::KIND_OBJECT) {
                    $interfaces = $type->getConfig()->getInterfaces();

                    if ($interfaces) {
                        foreach ($interfaces as $interface) {
                            if ($interface->getName() == $value->getName()) {
                                $possibleTypes[] = $type;
                            }

                            if ($interface instanceof AbstractInterfaceType) {
                                foreach ($interface->getImplementations() as $implementationType) {
                                    $possibleTypes[] = $implementationType;
                                }
                            }
                        }
                    }
                }
            }

            return \array_unique($possibleTypes);
        } elseif ($value->getKind() == TypeMap::KIND_UNION) {
            /** @var $value AbstractUnionType */
            return $value->getTypes();
        }

        return null;
    }
    
    /**
     * @throws ConfigurationException
     */
    public function build(ObjectTypeConfig $config): void
    {
        $config
            ->addField('name', TypeMap::TYPE_STRING)
            ->addField('kind', new NonNullType(TypeMap::TYPE_STRING))
            ->addField('description', TypeMap::TYPE_STRING)
            ->addField('ofType', [
                'type'    => new QueryType(),
                'resolve' => [$this, 'resolveOfType']
            ])
            ->addField(new Field([
                'name'    => 'inputFields',
                'type'    => new ListType(new NonNullType(new InputValueType())),
                'resolve' => [$this, 'resolveInputFields']
            ]))
            ->addField(new Field([
                'name'    => 'enumValues',
                'args'    => [
                    'includeDeprecated' => [
                        'type'    => new BooleanType(),
                        'defaultValue' => false
                    ]
                ],
                'type'    => new ListType(new NonNullType(new EnumValueType())),
                'resolve' => [$this, 'resolveEnumValues']
            ]))
            ->addField(new Field([
                'name'    => 'fields',
                'args'    => [
                    'includeDeprecated' => [
                        'type'    => new BooleanType(),
                        'defaultValue' => false
                    ]
                ],
                'type'    => new ListType(new NonNullType(new FieldType())),
                'resolve' => [$this, 'resolveFields']
            ]))
            ->addField(new Field([
                'name'    => 'interfaces',
                'type'    => new ListType(new NonNullType(new QueryType())),
                'resolve' => [$this, 'resolveInterfaces']
            ]))
            ->addField('possibleTypes', [
                'type'    => new ListType(new NonNullType(new QueryType())),
                'resolve' => [$this, 'resolvePossibleTypes']
            ]);
    }

}
