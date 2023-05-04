<?php

namespace Dreamlabs\Tests\Library\Validator;


use PHPUnit\Framework\TestCase;
use Exception;
use Dreamlabs\GraphQL\Schema\Schema;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Object\ObjectType;
use Dreamlabs\GraphQL\Type\Scalar\IntType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;
use Dreamlabs\GraphQL\Validator\SchemaValidator\SchemaValidator;
use Dreamlabs\Tests\DataProvider\TestEmptySchema;
use Dreamlabs\Tests\DataProvider\TestInterfaceType;

class SchemaValidatorTest extends TestCase
{
    /**
     * @expectedException \Dreamlabs\GraphQL\Exception\ConfigurationException
     */
    public function testInvalidSchema(): void
    {
        $validator = new SchemaValidator();
        $validator->validate(new TestEmptySchema());
    }

    /**
     * @expectedException \Dreamlabs\GraphQL\Exception\ConfigurationException
     * @expectedExceptionMessage Implementation of TestInterface is invalid for the field name
     */
    public function testInvalidInterfacesSimpleType(): void
    {
        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'user' => new ObjectType([
                        'name'       => 'User',
                        'fields'     => [
                            'name' => new IntType(),
                        ],
                        'interfaces' => [new TestInterfaceType()]
                    ])
                ],
            ])
        ]);

        $validator = new SchemaValidator();
        $validator->validate($schema);
    }

    /**
     * @expectedException \Dreamlabs\GraphQL\Exception\ConfigurationException
     * @expectedExceptionMessage Implementation of TestInterface is invalid for the field name
     */
    public function testInvalidInterfacesCompositeType(): void
    {
        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'user' => new ObjectType([
                        'name'       => 'User',
                        'fields'     => [
                            'name' => new NonNullType(new StringType()),
                        ],
                        'interfaces' => [new TestInterfaceType()]
                    ])
                ],
            ])
        ]);

        $validator = new SchemaValidator();
        $validator->validate($schema);
    }

    /**
     * @expectedException \Dreamlabs\GraphQL\Exception\ConfigurationException
     * @expectedExceptionMessage Implementation of TestInterface is invalid for the field name
     */
    public function testInvalidInterfaces(): void
    {
        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'user' => new ObjectType([
                        'name'       => 'User',
                        'fields'     => [
                            'name' => new IntType(),
                        ],
                        'interfaces' => [new TestInterfaceType()]
                    ])
                ],
            ])
        ]);

        $validator = new SchemaValidator();
        $validator->validate($schema);
    }

    public function testValidSchema(): void
    {
        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'user' => new ObjectType([
                        'name'       => 'User',
                        'fields'     => [
                            'name' => new StringType(),
                        ],
                        'interfaces' => [new TestInterfaceType()]
                    ])
                ],
            ])
        ]);

        $validator = new SchemaValidator();

        try {
            $validator->validate($schema);
            $this->assertTrue(true);
        } catch (Exception) {
            $this->assertTrue(false);
        }
    }
}
