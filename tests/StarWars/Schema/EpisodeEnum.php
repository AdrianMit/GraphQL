<?php

namespace Dreamlabs\Tests\StarWars\Schema;


use Dreamlabs\GraphQL\Type\Enum\AbstractEnumType;

class EpisodeEnum extends AbstractEnumType
{

    public function getValues()
    {
        return [
            [
                'value'       => 4,
                'name'        => 'NEWHOPE',
                'description' => 'Released in 1977.'
            ],
            [
                'value'       => 5,
                'name'        => 'EMPIRE',
                'description' => 'Released in 1980.'
            ],
            [
                'value'       => 6,
                'name'        => 'JEDI',
                'description' => 'Released in 1983.'
            ],
        ];
    }

    /**
     * @return String type name
     */
    public function getName(): string
    {
        return 'Episode';
    }
}
