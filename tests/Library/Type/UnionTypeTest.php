<?php

namespace Dreamlabs\Tests\Library\Type;

use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Type\Object\ObjectType;
use Dreamlabs\GraphQL\Type\Scalar\IntType;
use Dreamlabs\GraphQL\Type\TypeMap;
use Dreamlabs\GraphQL\Type\Union\UnionType;
use Dreamlabs\GraphQL\Validator\ConfigValidator\ConfigValidator;
use Dreamlabs\Tests\DataProvider\TestObjectType;
use Dreamlabs\Tests\DataProvider\TestUnionType;

class UnionTypeTest extends TestCase
{

    public function testInlineCreation(): void
    {
        $object = new ObjectType([
            'name' => 'TestObject',
            'fields' => ['id' => ['type' => new IntType()]]
        ]);

        $type = new UnionType([
            'name'        => 'Car',
            'description' => 'Union collect cars types',
            'types'       => [
                new TestObjectType(),
                $object
            ],
            'resolveType' => fn($type) => $type
        ]);

        $this->assertEquals('Car', $type->getName());
        $this->assertEquals('Union collect cars types', $type->getDescription());
        $this->assertEquals([new TestObjectType(), $object], $type->getTypes());
        $this->assertEquals('test', $type->resolveType('test'));
        $this->assertEquals(TypeMap::KIND_UNION, $type->getKind());
        $this->assertEquals($type, $type->getNamedType());
        $this->assertTrue($type->isValidValue(true));
    }

    public function testObjectCreation(): void
    {
        $type = new TestUnionType();

        $this->assertEquals('TestUnion', $type->getName());
        $this->assertEquals('Union collect cars types', $type->getDescription());
        $this->assertEquals([new TestObjectType()], $type->getTypes());
        $this->assertEquals('test', $type->resolveType('test'));
    }

    /**
     * @expectedException Dreamlabs\GraphQL\Exception\ConfigurationException
     */
    public function testInvalidTypesWithScalar(): void
    {
        $type = new UnionType([
            'name'        => 'Car',
            'description' => 'Union collect cars types',
            'types'       => [
                'test', new IntType()
            ],
            'resolveType' => fn($type) => $type
        ]);
        ConfigValidator::getInstance()->assertValidConfig($type->getConfig());
    }

    /**
     * @expectedException Dreamlabs\GraphQL\Exception\ConfigurationException
     */
    public function testInvalidTypes(): void
    {
        $type = new UnionType([
            'name'        => 'Car',
            'description' => 'Union collect cars types',
            'types'       => [
                new IntType()
            ],
            'resolveType' => fn($type) => $type
        ]);
        ConfigValidator::getInstance()->assertValidConfig($type->getConfig());
    }
}
