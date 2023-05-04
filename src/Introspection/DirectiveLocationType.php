<?php

namespace Dreamlabs\GraphQL\Introspection;

use Dreamlabs\GraphQL\Directive\DirectiveLocation;
use Dreamlabs\GraphQL\Type\Enum\AbstractEnumType;

class DirectiveLocationType extends AbstractEnumType
{
    
    public const QUERY               = DirectiveLocation::QUERY;
    public const MUTATION            = DirectiveLocation::MUTATION;
    public const FIELD               = DirectiveLocation::FIELD;
    public const FIELD_DEFINITION    = DirectiveLocation::FIELD_DEFINITION;
    public const FRAGMENT_DEFINITION = DirectiveLocation::FRAGMENT_DEFINITION;
    public const FRAGMENT_SPREAD     = DirectiveLocation::FRAGMENT_SPREAD;
    public const INLINE_FRAGMENT     = DirectiveLocation::INLINE_FRAGMENT;
    public const ENUM_VALUE          = DirectiveLocation::ENUM_VALUE;
    
    public function getName(): string
    {
        return '__DirectiveLocation';
    }
    
    public function getValues(): array
    {
        return [
            ['name' => 'QUERY', 'value' => self::QUERY],
            ['name' => 'MUTATION', 'value' => self::MUTATION],
            ['name' => 'FIELD', 'value' => self::FIELD],
            ['name' => 'FIELD_DEFINITION', 'value' => self::FIELD_DEFINITION],
            ['name' => 'FRAGMENT_DEFINITION', 'value' => self::FRAGMENT_DEFINITION],
            ['name' => 'FRAGMENT_SPREAD', 'value' => self::FRAGMENT_SPREAD],
            ['name' => 'INLINE_FRAGMENT', 'value' => self::INLINE_FRAGMENT],
            ['name' => 'ENUM_VALUE', 'value' => self::ENUM_VALUE],
        ];
    }
    
}
