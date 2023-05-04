<?php

namespace Dreamlabs\Tests\Issues\Issue201;

use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Schema\Schema;
use Dreamlabs\GraphQL\Type\Object\ObjectType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;
use Dreamlabs\GraphQL\Validator\SchemaValidator\SchemaValidator;

class Issue201Test extends TestCase
{

    /**
     * @throws \Dreamlabs\GraphQL\Exception\ConfigurationException
     * @expectedException \Dreamlabs\GraphQL\Exception\ConfigurationException
     * @expectedExceptionMessage Type "user" was defined more than once
     */
    public function testExceptionOnDuplicateTypeName(): void
    {
        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'user' => [
                        'type' => new StringType(),
                    ],
                ],
            ]),
        ]);

        $schema->getQueryType()->addFields([
            'user' => new StringType(),
        ]);

        $schemaValidator = new SchemaValidator();
        $schemaValidator->validate($schema);
    }
}
