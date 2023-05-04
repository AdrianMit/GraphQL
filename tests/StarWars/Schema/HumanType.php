<?php

namespace Dreamlabs\Tests\StarWars\Schema;


use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;
use Dreamlabs\GraphQL\Type\ListType\ListType;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\Scalar\IdType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;
use Dreamlabs\GraphQL\Type\TypeMap;

class HumanType extends AbstractObjectType
{

    public function build(ObjectTypeConfig $config): void
    {
        $config
            ->addField('id', new NonNullType(new IdType()))
            ->addField('name', new NonNullType(new StringType()))
            ->addField('friends', [
                'type'    => new ListType(new CharacterInterface()),
                'resolve' => fn($droid): array => StarWarsData::getFriends($droid),
            ])
            ->addField('appearsIn', new ListType(new EpisodeEnum()))
            ->addField('homePlanet', TypeMap::TYPE_STRING);
    }

    public function getInterfaces()
    {
        return [new CharacterInterface()];
    }

}
