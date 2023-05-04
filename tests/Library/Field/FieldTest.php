<?php

namespace Dreamlabs\Tests\Library\Field;


use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Config\Field\FieldConfig;
use Dreamlabs\GraphQL\Execution\ResolveInfo;
use Dreamlabs\GraphQL\Field\Field;
use Dreamlabs\GraphQL\Field\InputField;
use Dreamlabs\GraphQL\Type\Scalar\IdType;
use Dreamlabs\GraphQL\Type\Scalar\IntType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;
use Dreamlabs\GraphQL\Type\TypeMap;
use Dreamlabs\GraphQL\Validator\ConfigValidator\ConfigValidator;
use Dreamlabs\Tests\DataProvider\TestField;
use Dreamlabs\Tests\DataProvider\TestResolveInfo;

class FieldTest extends TestCase
{

    public function testInlineFieldCreation(): void
    {
        $field = new Field([
            'name' => 'id',
            'type' => new IdType()
        ]);
        $resolveInfo = TestResolveInfo::createTestResolveInfo($field);
        $this->assertEquals('id', $field->getName());
        $this->assertEquals(new IdType(), $field->getType());
        $this->assertEquals(null, $field->resolve('data', [], $resolveInfo));

        $fieldWithResolve = new Field([
            'name'    => 'title',
            'type'    => new StringType(),
            'resolve' => fn($value, array $args, ResolveInfo $info) => $info->getReturnType()->serialize($value)
        ]);
        $resolveInfo = TestResolveInfo::createTestResolveInfo($fieldWithResolve);
        $this->assertEquals('true', $fieldWithResolve->resolve(true, [], $resolveInfo), 'Resolve bool to string');

        $fieldWithResolve->setType(new IntType());
        $this->assertEquals(new StringType(), $fieldWithResolve->getType()->getName());

    }

    public function testObjectFieldCreation(): void
    {
        $field = new TestField();
        $resolveInfo = TestResolveInfo::createTestResolveInfo($field);

        $this->assertEquals('test', $field->getName());
        $this->assertEquals('description', $field->getDescription());
        $this->assertEquals(new IntType(), $field->getType());
        $this->assertEquals('test', $field->resolve('test', [], $resolveInfo));
    }

    public function testArgumentsTrait(): void
    {
        $testField = new TestField();
        $this->assertFalse($testField->hasArguments());

        $testField->addArgument(new InputField(['name' => 'id', 'type' => new IntType()]));
        $this->assertEquals([
            'id' => new InputField(['name' => 'id', 'type' => new IntType()])
        ], $testField->getArguments());

        $testField->addArguments([
            new InputField(['name' => 'name', 'type' => new StringType()])
        ]);
        $this->assertEquals([
            'id'   => new InputField(['name' => 'id', 'type' => new IntType()]),
            'name' => new InputField(['name' => 'name', 'type' => new StringType()]),
        ], $testField->getArguments());

        $testField->removeArgument('name');
        $this->assertFalse($testField->hasArgument('name'));
    }

    /**
     * @param $fieldConfig
     *
     * @dataProvider invalidFieldProvider
     * @expectedException Dreamlabs\GraphQL\Exception\ConfigurationException
     */
    public function testInvalidFieldParams($fieldConfig): void
    {
        $field = new Field($fieldConfig);
        ConfigValidator::getInstance()->assertValidConfig($field->getConfig());
    }

    public function invalidFieldProvider()
    {
        return [
            [
                [
                    'name' => 'id',
                    'type' => 'invalid type'
                ]
            ],
            [
                [
                    'type' => TypeMap::TYPE_FLOAT
                ]
            ]
        ];
    }

}
