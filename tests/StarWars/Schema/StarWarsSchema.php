<?php

namespace Dreamlabs\Tests\StarWars\Schema;


use Dreamlabs\GraphQL\Config\Schema\SchemaConfig;
use Dreamlabs\GraphQL\Schema\AbstractSchema;

class StarWarsSchema extends AbstractSchema
{

    public function build(SchemaConfig $config): void
    {
        $config->setQuery(new StarWarsQueryType());
    }

}
