<?php

namespace Dreamlabs\GraphQL\Type;

class TypeMap
{

    public const KIND_SCALAR       = 'SCALAR';
    public const KIND_OBJECT       = 'OBJECT';
    public const KIND_INTERFACE    = 'INTERFACE';
    public const KIND_UNION        = 'UNION';
    public const KIND_ENUM         = 'ENUM';
    public const KIND_INPUT_OBJECT = 'INPUT_OBJECT';
    public const KIND_LIST         = 'LIST';
    public const KIND_NON_NULL     = 'NON_NULL';

    public const TYPE_INT        = 'int';
    public const TYPE_FLOAT      = 'float';
    public const TYPE_STRING     = 'string';
    public const TYPE_BOOLEAN    = 'boolean';
    public const TYPE_ID         = 'id';
    public const TYPE_DATETIME   = 'datetime';
    public const TYPE_DATETIMETZ = 'datetimetz';
    public const TYPE_DATE       = 'date';
    public const TYPE_TIMESTAMP  = 'timestamp';


}
