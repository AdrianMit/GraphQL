<?php

namespace Examples\StarWars;


use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;
use Dreamlabs\GraphQL\Exception\ConfigurationException;
use Dreamlabs\GraphQL\Relay\Field\GlobalIdField;
use Dreamlabs\GraphQL\Relay\NodeInterfaceType;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\TypeMap;

class ShipType extends AbstractObjectType
{
    public const TYPE_KEY = 'ship';
    
    /**
     * @throws ConfigurationException
     */
    public function build(ObjectTypeConfig $config): void
    {
        $config
            ->addField(new GlobalIdField(self::TYPE_KEY))
            ->addField('name', ['type' => TypeMap::TYPE_STRING, 'description' => 'The name of the ship.']);
    }

    public function getOne($id): ?array
    {
        return TestDataProvider::getShip($id);
    }

    public function getDescription(): string
    {
        return 'A ship in the Star Wars saga';
    }

    public function getInterfaces(): array
    {
        return [new NodeInterfaceType()];
    }

}
