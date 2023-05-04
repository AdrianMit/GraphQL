<?php

namespace Dreamlabs\Tests\StarWars\Schema;

use Dreamlabs\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Dreamlabs\GraphQL\Type\ListType\ListType;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Scalar\IdType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class CharacterInterface extends AbstractInterfaceType
{
    public function build($config): void
    {
        $config
            ->addField('id', new NonNullType(new IdType()))
            ->addField('name', new NonNullType(new StringType()))
            ->addField('friends', [
                'type'    => new ListType(new CharacterInterface()),
                'resolve' => fn($value) => $value['friends']
            ])
            ->addField('appearsIn', new ListType(new EpisodeEnum()));
    }

    public function getDescription(): string
    {
        return 'A character in the Star Wars Trilogy';
    }

    public function getName(): string
    {
        return 'Character';
    }

    public function resolveType($object)
    {
        $humans = StarWarsData::humans();
        $droids = StarWarsData::droids();

        $id = $object['id'] ?? $object;

        if (isset($humans[$id])) {
            return new HumanType();
        }

        if (isset($droids[$id])) {
            return new DroidType();
        }

        return null;
    }

}
