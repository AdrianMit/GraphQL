<?php

namespace Dreamlabs\Tests\Library\Field;


use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;
use Dreamlabs\GraphQL\Field\Field;
use Dreamlabs\GraphQL\Type\Scalar\IntType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class FieldAwareConfigTraitTest extends TestCase
{

    public function testAddField(): void
    {
        $fieldsData = [
            'id' => [
                'type' => new IntType()
            ]
        ];
        $config     = new ObjectTypeConfig([
            'name'   => 'UserType',
            'fields' => $fieldsData
        ]);

        $this->assertTrue($config->hasFields());
        $idField = new Field(['name' => 'id', 'type' => new IntType()]);
        $idField->getName();
        $nameField = new Field(['name' => 'name', 'type' => new StringType()]);

        $this->assertEquals([
            'id' => $idField,
        ], $config->getFields());

        $config->addField($nameField);
        $this->assertEquals([
            'id'   => $idField,
            'name' => $nameField
        ], $config->getFields());

        $config->removeField('id');
        $this->assertEquals([
            'name' => $nameField
        ], $config->getFields());

        $config->addFields([
            'id' => $idField
        ]);
        $this->assertEquals([
            'name' => $nameField,
            'id'   => $idField,
        ], $config->getFields());

        $levelField = new Field(['name' => 'level', 'type' => new IntType()]);
        $config->addFields([
            $levelField
        ]);
        $this->assertEquals([
            'name'  => $nameField,
            'id'    => $idField,
            'level' => $levelField,
        ], $config->getFields());

    }

}
