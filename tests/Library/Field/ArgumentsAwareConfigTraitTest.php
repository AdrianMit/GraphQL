<?php

namespace Dreamlabs\Tests\Library\Field;


use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Config\Field\FieldConfig;
use Dreamlabs\GraphQL\Field\InputField;
use Dreamlabs\GraphQL\Type\Scalar\IntType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class ArgumentsAwareConfigTraitTest extends TestCase
{

    public function testArguments(): void
    {
        $argsData = [
            'id' => new IntType()
        ];
        $config   = new FieldConfig([
            'name' => 'UserType',
            'type' => new IntType(),
            'args' => $argsData
        ]);

        $this->assertTrue($config->hasArguments());
        $this->assertEquals([
            'id' => new InputField(['name' => 'id', 'type' => new IntType()]),
        ], $config->getArguments());

        $config->addArgument('name', new StringType());
        $this->assertEquals([
            'id'   => new InputField(['name' => 'id', 'type' => new IntType()]),
            'name' => new InputField(['name' => 'name', 'type' => new StringType()])
        ], $config->getArguments());

        $config->removeArgument('id');
        $this->assertEquals([
            'name' => new InputField(['name' => 'name', 'type' => new StringType()])
        ], $config->getArguments());

        $config->addArguments([
            'id' => new InputField(['name' => 'id', 'type' => new IntType()])
        ]);
        $this->assertEquals([
            'name' => new InputField(['name' => 'name', 'type' => new StringType()]),
            'id'   => new InputField(['name' => 'id', 'type' => new IntType()]),
        ], $config->getArguments());

        $config->addArguments([
            new InputField(['name' => 'level', 'type' => new IntType()])
        ]);
        $this->assertEquals([
            'name'  => new InputField(['name' => 'name', 'type' => new StringType()]),
            'id'    => new InputField(['name' => 'id', 'type' => new IntType()]),
            'level' => new InputField(['name' => 'level', 'type' => new IntType()]),
        ], $config->getArguments());

    }

}
