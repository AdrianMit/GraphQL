<?php

namespace Dreamlabs\Tests\Library\Type;


use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Field\Field;
use Dreamlabs\GraphQL\Type\Object\ObjectType;
use Dreamlabs\GraphQL\Type\Scalar\IntType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;
use Dreamlabs\GraphQL\Type\TypeMap;
use Dreamlabs\GraphQL\Validator\ConfigValidator\ConfigValidator;
use Dreamlabs\Tests\DataProvider\TestMutationObjectType;
use Dreamlabs\Tests\DataProvider\TestObjectType;

class ObjectTypeTest extends TestCase
{

    /**
     * @expectedException Dreamlabs\GraphQL\Exception\ConfigurationException
     */
    public function testCreatingInvalidObject(): void
    {
        new ObjectType([]);
    }

    /**
     * @expectedException Dreamlabs\GraphQL\Exception\ConfigurationException
     */
    public function testInvalidNameParam(): void
    {
        $type = new ObjectType([
            'name' => null
        ]);
        ConfigValidator::getInstance()->assertValidConfig($type->getConfig());
    }

    /**
     * @expectedException Dreamlabs\GraphQL\Exception\ConfigurationException
     */
    public function testInvalidFieldsParam(): void
    {
        $type = new ObjectType([
            'name'   => 'SomeName',
            'fields' => []
        ]);
        ConfigValidator::getInstance()->assertValidConfig($type->getConfig());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSerialize(): void
    {
        $object = new ObjectType([
            'name'   => 'SomeName',
            'fields' => [
                'name' => new StringType()
            ]
        ]);
        $object->serialize([]);
    }


    public function testNormalCreatingParam(): void
    {
        $objectType = new ObjectType([
            'name'        => 'Post',
            'fields'      => [
                'id' => new IntType()
            ],
            'description' => 'Post type description'
        ]);
        $this->assertEquals($objectType->getKind(), TypeMap::KIND_OBJECT);
        $this->assertEquals($objectType->getName(), 'Post');
        $this->assertEquals($objectType->getType(), $objectType);
        $this->assertEquals($objectType->getType()->getName(), 'Post');
        $this->assertEquals($objectType->getNamedType(), $objectType);

        $this->assertEmpty($objectType->getInterfaces());
        $this->assertTrue($objectType->isValidValue($objectType));
        $this->assertTrue($objectType->isValidValue(null));

        $this->assertEquals('Post type description', $objectType->getDescription());
    }

    public function testFieldsTrait(): void
    {
        $idField = new Field(['name' => 'id', 'type' => new IntType()]);
        $nameField = new Field(['name' => 'name', 'type' => new StringType()]);

        $objectType = new ObjectType([
            'name'        => 'Post',
            'fields'      => [
                $idField
            ],
            'description' => 'Post type description'
        ]);
        $this->assertTrue($objectType->hasFields());
        $this->assertEquals([
            'id' => $idField
        ], $objectType->getFields());

        $objectType->addField($nameField);
        $this->assertEquals([
            'id'   => $idField,
            'name' => $nameField,
        ], $objectType->getFields());
    }

    public function testExtendedClass(): void
    {
        $objectType = new TestObjectType();
        $this->assertEquals($objectType->getName(), 'TestObject');
        $this->assertEquals($objectType->getType(), $objectType, 'test type of extended object');

        $this->assertNull($objectType->getDescription());
    }

    public function testMutationObjectClass(): void
    {
        $mutation = new TestMutationObjectType();
        $this->assertEquals(new StringType(), $mutation->getType());
    }

}
