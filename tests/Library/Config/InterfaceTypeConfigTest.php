<?php

namespace Dreamlabs\Tests\Library\Config;


use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Config\Object\InterfaceTypeConfig;
use Dreamlabs\GraphQL\Type\Object\ObjectType;
use Dreamlabs\GraphQL\Type\Scalar\IntType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;
use Dreamlabs\GraphQL\Validator\ConfigValidator\ConfigValidator;
use Dreamlabs\Tests\DataProvider\TestInterfaceType;

class InterfaceTypeConfigTest extends TestCase
{

    public function testCreation(): void
    {
        $config = new InterfaceTypeConfig(['name' => 'Test'], null, false);
        $this->assertEquals($config->getName(), 'Test', 'Normal creation');
    }

    /**
     * @expectedException Dreamlabs\GraphQL\Exception\ConfigurationException
     */
    public function testConfigNoFields(): void
    {
        ConfigValidator::getInstance()->assertValidConfig(
            new InterfaceTypeConfig(['name' => 'Test', 'resolveType' => function (): void { }], null, true)
        );
    }

    /**
     * @expectedException Dreamlabs\GraphQL\Exception\ConfigurationException
     */
    public function testConfigNoResolve(): void
    {
        ConfigValidator::getInstance()->assertValidConfig(
            new InterfaceTypeConfig(['name' => 'Test', 'fields' => ['id' => new IntType()]], null, true)
        );
    }

    /**
     * @expectedException Dreamlabs\GraphQL\Exception\ConfigurationException
     */
    public function testConfigInvalidResolve(): void
    {
        $config = new InterfaceTypeConfig(['name' => 'Test', 'fields' => ['id' => new IntType()]], null, false);
        $config->resolveType(['invalid object']);
    }

    public function testInterfaces(): void
    {
        $interfaceConfig = new InterfaceTypeConfig([
            'name'        => 'Test',
            'fields'      => ['id' => new IntType()],
            'resolveType' => fn($object) => $object->getType()
        ], null, true);
        $object          = new ObjectType(['name' => 'User', 'fields' => ['name' => new StringType()]]);

        $this->assertEquals($interfaceConfig->getName(), 'Test');
        $this->assertEquals($interfaceConfig->resolveType($object), $object->getType());

        $testInterface                = new TestInterfaceType();
        $interfaceConfigWithNoResolve = new InterfaceTypeConfig([
            'name'   => 'Test',
            'fields' => ['id' => new IntType()]
        ], $testInterface, false);
        $this->assertEquals($interfaceConfigWithNoResolve->resolveType($object), $object);
    }


}
