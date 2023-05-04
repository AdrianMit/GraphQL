<?php

namespace Dreamlabs\Tests\DataProvider;

use Dreamlabs\GraphQL\Config\Schema\SchemaConfig;
use Dreamlabs\GraphQL\Schema\AbstractSchema;


class TestEmptySchema extends AbstractSchema
{
    public function build(SchemaConfig $config): void
    {
    }


    public function getName($config)
    {
        return 'TestSchema';
    }
}
