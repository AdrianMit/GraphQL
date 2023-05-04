<?php

namespace Dreamlabs\Tests\Issues\Issue193;

use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;
use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Config\Schema\SchemaConfig;
use Dreamlabs\GraphQL\Execution\Processor;
use Dreamlabs\GraphQL\Schema\AbstractSchema;
use Dreamlabs\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\Scalar\IntType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class Issue193Test extends TestCase
{
    public function testResolvedInterfacesShouldBeRegistered(): void
    {
        $schema    = new Issue193Schema();
        $processor = new Processor($schema);

        $processor->processPayload($this->getIntrospectionQuery(), []);
        $resp = $processor->getResponseData();

        $typeNames = array_map(fn($type) => $type['name'], $resp['data']['__schema']['types']);

        // Check that all types are discovered
        $this->assertContains('ContentBlockInterface', $typeNames);
        $this->assertContains('Post', $typeNames);
        $this->assertContains('Undiscovered', $typeNames);

        // Check that possibleTypes for interfaces are discovered
        $contentBlockInterfaceType = null;

        foreach ($resp['data']['__schema']['types'] as $type) {
            if ($type['name'] === 'ContentBlockInterface') {
                $contentBlockInterfaceType = $type;
                break;
            }
        }

        $this->assertNotNull($contentBlockInterfaceType);
        $this->assertEquals([
            ['name' => 'Post'],
            ['name' => 'Undiscovered'],
        ], $contentBlockInterfaceType['possibleTypes']);
    }

    private function getIntrospectionQuery()
    {
        return <<<TEXT
query IntrospectionQuery {
    __schema {
        types {
            kind
          	name
          	possibleTypes {
          	    name
          	}
        }
    }
}
TEXT;
    }
}

class Issue193Schema extends AbstractSchema
{
    public function build(SchemaConfig $config): void
    {
        $config->getQuery()->addField(
            'post',
            [
                'type' => new PostType(),
            ]
        );
    }
}

class PostType extends AbstractObjectType
{

    public function build(ObjectTypeConfig $config): void
    {
        $config->applyInterface(new ContentBlockInterface());
        $config->addFields([
            'likesCount' => new IntType(),
        ]);
    }

    public function getInterfaces()
    {
        return [new ContentBlockInterface()];
    }
}

class UndiscoveredType extends AbstractObjectType
{
    public function build(ObjectTypeConfig $config): void
    {
        $config->applyInterface(new ContentBlockInterface());
    }
}

class ContentBlockInterface extends AbstractInterfaceType
{
    public function build($config): void
    {
        $config->addField('title', new NonNullType(new StringType()));
        $config->addField('summary', new StringType());
    }

    public function resolveType($object): \Dreamlabs\Tests\Issues\Issue193\PostType|\Dreamlabs\Tests\Issues\Issue193\UndiscoveredType
    {
        if (isset($object['title'])) {
            return new PostType();
        }

        return new UndiscoveredType();
    }

    public function getImplementations()
    {
        return [
            new PostType(),
            new UndiscoveredType(),
        ];
    }
}
