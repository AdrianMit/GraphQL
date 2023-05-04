<?php

namespace Dreamlabs\Tests\Library\Type;


use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Type\Enum\EnumType;
use Dreamlabs\GraphQL\Type\TypeMap;
use Dreamlabs\GraphQL\Validator\ConfigValidator\ConfigValidator;
use Dreamlabs\Tests\DataProvider\TestEnumType;

class EnumTypeTest extends TestCase
{

    /**
     * @expectedException Dreamlabs\GraphQL\Exception\ConfigurationException
     */
    public function testInvalidInlineCreation(): void
    {
        new EnumType([]);
    }

    /**
     * @expectedException Dreamlabs\GraphQL\Exception\ConfigurationException
     */
    public function testInvalidEmptyParams(): void
    {
        $enumField = new EnumType([
            'values' => []
        ]);
        ConfigValidator::getInstance()->assertValidConfig($enumField->getConfig());

    }

    /**
     * @expectedException Dreamlabs\GraphQL\Exception\ConfigurationException
     */
    public function testInvalidValueParams(): void
    {
        $enumField = new EnumType([
            'values' => [
                'test'  => 'asd',
                'value' => 'asdasd'
            ]
        ]);
        ConfigValidator::getInstance()->assertValidConfig($enumField->getConfig());
    }

    /**
     * @expectedException Dreamlabs\GraphQL\Exception\ConfigurationException
     */
    public function testExistingNameParams(): void
    {
        $enumField = new EnumType([
            'values' => [
                [
                    'test'  => 'asd',
                    'value' => 'asdasd'
                ]
            ]
        ]);
        ConfigValidator::getInstance()->assertValidConfig($enumField->getConfig());
    }

    /**
     * @expectedException Dreamlabs\GraphQL\Exception\ConfigurationException
     */
    public function testInvalidNameParams(): void
    {
        $enumField = new EnumType([
            'values' => [
                [
                    'name'  => false,
                    'value' => 'asdasd'
                ]
            ]
        ]);
        ConfigValidator::getInstance()->assertValidConfig($enumField->getConfig());
    }

    /**
     * @expectedException Dreamlabs\GraphQL\Exception\ConfigurationException
     */
    public function testWithoutValueParams(): void
    {
        $enumField = new EnumType([
            'values' => [
                [
                    'name' => 'TEST_ENUM',
                ]
            ]
        ]);
        ConfigValidator::getInstance()->assertValidConfig($enumField->getConfig());
    }

    public function testNormalCreatingParams(): void
    {
        $valuesData = [
            [
                'name'  => 'ENABLE',
                'value' => true
            ],
            [
                'name'  => 'DISABLE',
                'value' => 'disable'
            ]
        ];
        $enumType   = new EnumType([
            'name'   => 'BoolEnum',
            'values' => $valuesData
        ]);

        $this->assertEquals($enumType->getKind(), TypeMap::KIND_ENUM);
        $this->assertEquals($enumType->getName(), 'BoolEnum');
        $this->assertEquals($enumType->getType(), $enumType);
        $this->assertEquals($enumType->getNamedType(), $enumType);

        $this->assertFalse($enumType->isValidValue($enumType));
        $this->assertTrue($enumType->isValidValue(null));

        $this->assertTrue($enumType->isValidValue(true));
        $this->assertTrue($enumType->isValidValue('disable'));

        $this->assertNull($enumType->serialize('invalid value'));
        $this->assertNull($enumType->parseValue('invalid literal'));
        $this->assertTrue($enumType->parseValue('ENABLE'));

        $this->assertEquals($valuesData, $enumType->getValues());
    }

    public function testExtendedObject(): void
    {
        $testEnumType = new TestEnumType();
        $this->assertEquals('TestEnum', $testEnumType->getName());
    }

}
