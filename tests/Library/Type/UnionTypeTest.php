<?php
/**
 * Date: 13.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\Tests\Library\Type;

use PHPUnit_Framework_TestCase;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Type\Union\UnionType;
use Youshido\GraphQL\Validator\ConfigValidator\ConfigValidator;
use Youshido\Tests\DataProvider\TestObjectType;
use Youshido\Tests\DataProvider\TestUnionType;

class UnionTypeTest extends PHPUnit_Framework_TestCase
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
     * @expectedException Youshido\GraphQL\Exception\ConfigurationException
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
     * @expectedException Youshido\GraphQL\Exception\ConfigurationException
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
