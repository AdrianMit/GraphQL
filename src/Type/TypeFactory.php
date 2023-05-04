<?php

namespace Dreamlabs\GraphQL\Type;


use Dreamlabs\GraphQL\Exception\ConfigurationException;
use Dreamlabs\GraphQL\Type\Scalar\AbstractScalarType;

class TypeFactory
{
    private static array $objectsHash = [];

    /**
     * @param string $type
     *
     * @throws ConfigurationException
     * @return AbstractScalarType
     */
    public static function getScalarType($type)
    {
        if (TypeService::isScalarType($type)) {
            if (is_object($type)) {
                return $type;
            }
            if (empty(self::$objectsHash[$type])) {
                $name = ucfirst($type);

                $name = $name == 'Datetime' ? 'DateTime' : $name;
                $name = $name == 'Datetimetz' ? 'DateTimeTz' : $name;

                $className                = 'Dreamlabs\GraphQL\Type\Scalar\\' . $name . 'Type';
                self::$objectsHash[$type] = new $className();
            }

            return self::$objectsHash[$type];
        } else {
            throw new ConfigurationException('Configuration problem with type ' . $type);
        }
    }

    /**
     * @return string[]
     */
    public static function getScalarTypesNames()
    {
        return [
            TypeMap::TYPE_INT,
            TypeMap::TYPE_FLOAT,
            TypeMap::TYPE_STRING,
            TypeMap::TYPE_BOOLEAN,
            TypeMap::TYPE_ID,
            TypeMap::TYPE_DATETIME,
            TypeMap::TYPE_DATE,
            TypeMap::TYPE_TIMESTAMP,
            TypeMap::TYPE_DATETIMETZ,
        ];
    }
}
