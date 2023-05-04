<?php

namespace Examples\StarWars;


use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;
use Dreamlabs\GraphQL\Exception\ConfigurationException;
use Dreamlabs\GraphQL\Relay\Connection\ArrayConnection;
use Dreamlabs\GraphQL\Relay\Connection\Connection;
use Dreamlabs\GraphQL\Relay\Field\GlobalIdField;
use Dreamlabs\GraphQL\Relay\NodeInterfaceType;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\Scalar\IntType;
use Dreamlabs\GraphQL\Type\TypeMap;

class FactionType extends AbstractObjectType
{
    public const TYPE_KEY = 'faction';
    
    /**
     * @throws ConfigurationException
     */
    public function build(ObjectTypeConfig $config): void
    {
        $config
            ->addField(new GlobalIdField(self::TYPE_KEY))
            ->addField('factionId', [
                'type' => new IntType(),
                'resolve' => function($value) {
                    return $value['id'];
                }
            ])
            ->addField('name', [
                'type'        => TypeMap::TYPE_STRING,
                'description' => 'The name of the faction.'
            ])
            ->addField('ships', [
                'type'        => Connection::connectionDefinition(new ShipType()),
                'description' => 'The ships used by the faction',
                'args'        => Connection::connectionArgs(),
                'resolve'     => function ($value = null, $args = [], $type = null) {
                    return ArrayConnection::connectionFromArray(array_map(function ($id) {
                        return TestDataProvider::getShip($id);
                    }, $value['ships']), $args);
                }
            ]);

    }

    public function getDescription(): string
    {
        return 'A faction in the Star Wars saga';
    }

    public function getOne($id): ?array
    {
        return TestDataProvider::getFaction($id);
    }

    public function getInterfaces(): array
    {
        return [new NodeInterfaceType()];
    }

}
