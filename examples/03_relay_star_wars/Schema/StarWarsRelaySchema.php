<?php

namespace Examples\StarWars;

use Dreamlabs\GraphQL\Config\Schema\SchemaConfig;
use Dreamlabs\GraphQL\Exception\ConfigurationException;
use Dreamlabs\GraphQL\Field\InputField;
use Dreamlabs\GraphQl\Relay\Connection\ArrayConnection;
use Dreamlabs\GraphQL\Relay\Connection\Connection;
use Dreamlabs\GraphQL\Relay\Fetcher\CallableFetcher;
use Dreamlabs\GraphQL\Relay\Field\NodeField;
use Dreamlabs\GraphQL\Relay\RelayMutation;
use Dreamlabs\GraphQL\Schema\AbstractSchema;
use Dreamlabs\GraphQL\Type\ListType\ListType;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class StarWarsRelaySchema extends AbstractSchema
{
    /**
     * @throws ConfigurationException
     */
    public function build(SchemaConfig $config): void
    {
        $fetcher = new CallableFetcher(
            function ($type, $id) {
                return match ($type) {
                    FactionType::TYPE_KEY => TestDataProvider::getFaction($id),
                    ShipType::TYPE_KEY => TestDataProvider::getShip($id),
                    default => null,
                };
            },
            function ($object) {
                return $object && array_key_exists('ships', $object) ? new FactionType() : new ShipType();
            }
        );
        
        $config->getQuery()
               ->addField(new NodeField($fetcher))
               ->addField('rebels', [
                   'type'    => new FactionType(),
                   'resolve' => function () {
                       return TestDataProvider::getFaction('rebels');
                   },
               ])
               ->addField('empire', [
                   'type'    => new FactionType(),
                   'resolve' => function () {
                       return TestDataProvider::getFaction('empire');
                   },
               ])
               ->addField('factions', [
                   'type'    => new ListType(new FactionType()),
                   'args'    => [
                       'names' => [
                           'type' => new ListType(new StringType()),
                       ],
                   ],
                   'resolve' => function ($value = null, $args, $info) {
                       return TestDataProvider::getByNames($args['names']);
                   },
               ]);
        
        
        $config->getMutation()
               ->addField(
                   RelayMutation::buildMutation(
                       'introduceShip',
                       [
                           new InputField(['name' => 'shipName', 'type' => new NonNullType(new StringType())]),
                           new InputField(['name' => 'factionId', 'type' => new NonNullType(new StringType())]),
                       ],
                       [
                           'newShipEdge' => [
                               'type'    => Connection::edgeDefinition(new ShipType(), 'newShip'),
                               'resolve' => function ($value) {
                                   $allShips = TestDataProvider::getShips();
                                   $newShip  = TestDataProvider::getShip($value['shipId']);
                                   
                                   return [
                                       'cursor' => ArrayConnection::cursorForObjectInConnection($allShips, $newShip),
                                       'node'   => $newShip,
                                   ];
                               },
                           ],
                           'faction'     => [
                               'type'    => new FactionType(),
                               'resolve' => function ($value) {
                                   return TestDataProvider::getFaction($value['factionId']);
                               },
                           ],
                       ],
                       function ($value, $args, $info) {
                           $newShip = TestDataProvider::createShip($args['shipName'], $args['factionId']);
                           
                           return [
                               'shipId'    => $newShip['id'],
                               'factionId' => $args['factionId'],
                           ];
                       }
                   )
               );
    }
}
