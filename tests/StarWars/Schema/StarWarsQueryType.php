<?php

namespace Dreamlabs\Tests\StarWars\Schema;


use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;
use Dreamlabs\GraphQL\Field\Field;
use Dreamlabs\GraphQL\Field\FieldFactory;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\Scalar\IdType;

class StarWarsQueryType extends AbstractObjectType
{

    /**
     * @return String type name
     */
    public function getName(): string
    {
        return 'Query';
    }

    public function build(ObjectTypeConfig $config): void
    {
        $config
            ->addField('hero', [
                'type'    => new CharacterInterface(),
                'args'    => [
                    'episode' => ['type' => new EpisodeEnum()]
                ],
                'resolve' => fn($root, $args): array => StarWarsData::getHero($args['episode'] ?? null),
            ])
            ->addField(new Field([
                'name'    => 'human',
                'type'    => new HumanType(),
                'args'    => [
                    'id' => new IdType()
                ],
                'resolve' => function ($value = null, $args = []) {
                    $humans = StarWarsData::humans();

                    return $humans[$args['id']] ?? null;
                }
            ]))
            ->addField(new Field([
                'name'    => 'droid',
                'type'    => new DroidType(),
                'args'    => [
                    'id' => new IdType()
                ],
                'resolve' => function ($value = null, $args = []) {
                    $droids = StarWarsData::droids();

                    return $droids[$args['id']] ?? null;
                }
            ]));
    }
}
