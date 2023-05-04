<?php

namespace Dreamlabs\GraphQL\Introspection\Field;


use Dreamlabs\GraphQL\Execution\ResolveInfo;
use Dreamlabs\GraphQL\Field\AbstractField;
use Dreamlabs\GraphQL\Introspection\SchemaType;
use Dreamlabs\GraphQL\Schema\AbstractSchema;

class SchemaField extends AbstractField
{
    /**
     * @return SchemaType
     */
    public function getType(): SchemaType
    {
        return new SchemaType();
    }
    
    public function getName(): string
    {
        return '__schema';
    }
    
    public function resolve($value, array $args, ResolveInfo $info): AbstractSchema
    {
        return $info->getExecutionContext()->getSchema();
    }
    
    
}
