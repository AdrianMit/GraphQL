<?php

namespace Dreamlabs\Tests\Parser;


use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Parser\Ast\Argument;
use Dreamlabs\GraphQL\Parser\Ast\ArgumentValue\InputList;
use Dreamlabs\GraphQL\Parser\Ast\ArgumentValue\InputObject;
use Dreamlabs\GraphQL\Parser\Ast\ArgumentValue\Literal;
use Dreamlabs\GraphQL\Parser\Ast\ArgumentValue\Variable;
use Dreamlabs\GraphQL\Parser\Ast\Field;
use Dreamlabs\GraphQL\Parser\Ast\Fragment;
use Dreamlabs\GraphQL\Parser\Ast\FragmentReference;
use Dreamlabs\GraphQL\Parser\Ast\Query;
use Dreamlabs\GraphQL\Parser\Ast\TypedFragmentReference;
use Dreamlabs\GraphQL\Parser\Location;

class AstTest extends TestCase
{

    public function testArgument(): void
    {
        $argument = new Argument('test', new Literal('test', new Location(1,1)), new Location(1,1));

        $this->assertNotNull($argument->getValue());
        $this->assertEquals($argument->getName(), 'test');

        $argument->setName('test2');
        $argument->setValue('some value');

        $this->assertEquals($argument->getName(), 'test2');
        $this->assertEquals($argument->getValue(), 'some value');
    }

    public function testField(): void
    {
        $field = new Field('field', null, [], [], new Location(1,1));

        $this->assertEquals($field->getName(), 'field');
        $this->assertEmpty($field->getArguments());
        $this->assertFalse($field->hasArguments());

        $field->setAlias('alias');
        $field->setName('alias');
        $this->assertEquals($field->getAlias(), 'alias');
        $this->assertEquals($field->getName(), 'alias');

        $field->addArgument(new Argument('argument', new Literal('argument value', new Location(1,1)), new Location(1,1)));
        $this->assertTrue($field->hasArguments());
        $this->assertEquals(['argument' => 'argument value'], $field->getKeyValueArguments());
    }

    public function testFragment(): void
    {
        $fields = [
            new Field('field', null, [], [], new Location(1,1))
        ];

        $fragment = new Fragment('shipInfo', 'Ship', [], $fields, new Location(1,1));

        $this->assertEquals('shipInfo', $fragment->getName());
        $this->assertEquals('Ship', $fragment->getModel());
        $this->assertEquals($fields, $fragment->getFields());

        $fragment->setName('largeShipInfo');
        $this->assertEquals('largeShipInfo', $fragment->getName());

        $fragment->setModel('Boat');
        $this->assertEquals('Boat', $fragment->getModel());

        $newField = [
            new Field('id', null, [], [], new Location(1,1))
        ];
        $fragment->setFields($newField);
        $this->assertEquals($newField, $fragment->getFields());

        $this->assertFalse($fragment->isUsed());
        $fragment->setUsed(true);
        $this->assertTrue($fragment->isUsed());
    }

    public function testFragmentReference(): void
    {
        $reference = new FragmentReference('shipInfo', new Location(1,1));

        $this->assertEquals('shipInfo', $reference->getName());

        $reference->setName('largeShipInfo');
        $this->assertEquals('largeShipInfo', $reference->getName());
    }

    public function testTypedFragmentReference(): void
    {
        $fields = [
            new Field('id', null, [], [], new Location(1,1))
        ];

        $reference = new TypedFragmentReference('Ship', $fields, [], new Location(1,1));

        $this->assertEquals('Ship', $reference->getTypeName());
        $this->assertEquals($fields, $reference->getFields());

        $reference->setTypeName('BigBoat');
        $this->assertEquals('BigBoat', $reference->getTypeName());

        $newFields = [
            new Field('name', null, [], [], new Location(1,1)),
            new Field('id', null, [], [], new Location(1,1))
        ];

        $reference->setFields($newFields);
        $this->assertEquals($newFields, $reference->getFields());
    }

    public function testQuery(): void
    {
        $arguments = [
            new Argument('limit', new Literal('10', new Location(1,1)), new Location(1,1))
        ];

        $fields = [
            new Field('id', null, [], [], new Location(1,1))
        ];

        $query = new Query('ships', 'lastShips', $arguments, $fields,[], new Location(1,1));

        $this->assertEquals('ships', $query->getName());
        $this->assertEquals('lastShips', $query->getAlias());
        $this->assertEquals(['limit' => $arguments[0]], $query->getArguments());
        $this->assertEquals(['limit' => '10'], $query->getKeyValueArguments());
        $this->assertEquals($fields, $query->getFields());
        $this->assertTrue($query->hasArguments());
        $this->assertTrue($query->hasFields());

        $query->setFields([]);
        $query->setArguments([]);

        $this->assertEmpty($query->getArguments());
        $this->assertEmpty($query->getFields());
        $this->assertEmpty($query->getKeyValueArguments());

        $this->assertFalse($query->hasArguments());
        $this->assertFalse($query->hasFields());

        $query->addArgument(new Argument('offset', new Literal(10, new Location(1,1)), new Location(1,1)));
        $this->assertTrue($query->hasArguments());
    }

    public function testArgumentValues(): void
    {
        $list = new InputList(['a', 'b'], new Location(1,1));
        $this->assertEquals(['a', 'b'], $list->getValue());
        $list->setValue(['a']);
        $this->assertEquals(['a'], $list->getValue());

        $inputObject = new InputObject(['a', 'b'], new Location(1,1));
        $this->assertEquals(['a', 'b'], $inputObject->getValue());
        $inputObject->setValue(['a']);
        $this->assertEquals(['a'], $inputObject->getValue());

        $literal = new Literal('text', new Location(1,1));
        $this->assertEquals('text', $literal->getValue());
        $literal->setValue('new text');
        $this->assertEquals('new text', $literal->getValue());
    }

    public function testVariable(): void
    {
        $variable = new Variable('id', 'int', false, false, new Location(1,1));

        $this->assertEquals('id', $variable->getName());
        $this->assertEquals('int', $variable->getTypeName());
        $this->assertFalse($variable->isNullable());
        $this->assertFalse($variable->isArray());

        $variable->setTypeName('string');
        $this->assertEquals('string', $variable->getTypeName());

        $variable->setName('limit');
        $this->assertEquals('limit', $variable->getName());

        $variable->setIsArray(true);
        $variable->setNullable(true);

        $this->assertTrue($variable->isNullable());
        $this->assertTrue($variable->isArray());

        $variable->setValue(new Literal('text', new Location(1,1)));
        $this->assertEquals(new Literal('text', new Location(1,1)), $variable->getValue());
    }

    /**
     * @expectedException \LogicException
     */
    public function testVariableLogicException(): void
    {
        $variable = new Variable('id', 'int', false, false, new Location(1,1));
        $variable->getValue();
    }
}
