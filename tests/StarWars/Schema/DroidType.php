<?php

namespace Dreamlabs\Tests\StarWars\Schema;


use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;
use Dreamlabs\GraphQL\Type\TypeMap;

class DroidType extends HumanType
{

    /**
     * @return String type name
     */
    public function getName(): string
    {
        return 'Droid';
    }

    public function build(ObjectTypeConfig $config): void
    {
        parent::build($config);

        $config->getField('friends')->getConfig()->set('resolve', fn($droid): array => StarWarsData::getFriends($droid));

        $config
            ->addField('primaryFunction', TypeMap::TYPE_STRING);
    }

    public function getInterfaces()
    {
        return [new CharacterInterface()];
    }
}
