<?php

namespace Dreamlabs\GraphQL\Introspection\Field;

use Dreamlabs\GraphQL\Config\Field\FieldConfig;
use Dreamlabs\GraphQL\Execution\ResolveInfo;
use Dreamlabs\GraphQL\Field\AbstractField;
use Dreamlabs\GraphQL\Field\InputField;
use Dreamlabs\GraphQL\Introspection\QueryType;
use Dreamlabs\GraphQL\Introspection\Traits\TypeCollectorTrait;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class TypeDefinitionField extends AbstractField
{

    use TypeCollectorTrait;

    public function resolve($value, array $args, ResolveInfo $info)
    {
        $schema = $info->getExecutionContext()->getSchema();
        $this->collectTypes($schema->getQueryType());
        $this->collectTypes($schema->getMutationType());

        foreach ($schema->getTypesList()->getTypes() as $type) {
            $this->collectTypes($type);
        }

        foreach ($this->types as $name => $info) {
            if ($name == $args['name']) {
                return $info;
            }
        }

        return null;
    }

    public function build(FieldConfig $config): void
    {
        $config->addArgument(new InputField([
            'name' => 'name',
            'type' => new NonNullType(new StringType())
        ]));
    }


    /**
     * @return String type name
     */
    public function getName()
    {
        return '__type';
    }

    /**
     * @return AbstractObjectType
     */
    public function getType(): QueryType
    {
        return new QueryType();
    }
}
