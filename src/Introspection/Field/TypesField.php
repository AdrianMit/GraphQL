<?php

namespace Dreamlabs\GraphQL\Introspection\Field;


use Dreamlabs\GraphQL\Execution\ResolveInfo;
use Dreamlabs\GraphQL\Field\AbstractField;
use Dreamlabs\GraphQL\Introspection\QueryType;
use Dreamlabs\GraphQL\Introspection\Traits\TypeCollectorTrait;
use Dreamlabs\GraphQL\Schema\AbstractSchema;
use Dreamlabs\GraphQL\Type\ListType\ListType;

class TypesField extends AbstractField
{
    
    use TypeCollectorTrait;
    
    public function getType(): ListType
    {
        return new ListType(new QueryType());
    }
    
    public function getName(): string
    {
        return 'types';
    }
    
    public function resolve($value, array $args, ResolveInfo $info): array
    {
        /** @var $value AbstractSchema $a */
        $this->types = [];
        $this->collectTypes($value->getQueryType());
        
        if ($value->getMutationType()->hasFields()) {
            $this->collectTypes($value->getMutationType());
        }
        
        foreach ($value->getTypesList()->getTypes() as $type) {
            $this->collectTypes($type);
        }
        
        return array_values($this->types);
    }
    
}
