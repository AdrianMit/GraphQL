<?php

namespace Youshido\Tests\Parser;

use PHPUnit_Framework_TestCase;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\Variable;
use Youshido\GraphQL\Parser\Location;

class VariableTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test if variable value equals expected value
     *
     * @dataProvider variableProvider
     */
    public function testGetValue($actual, $expected): void
    {
        $var = new Variable('foo', 'bar', false, false, true, new Location(1,1));
        $var->setValue($actual);
        $this->assertEquals($var->getValue(), $expected);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Value is not set for variable "foo"
     */
    public function testGetNullValueException(): void
    {
        $var = new Variable('foo', 'bar', false, false, true, new Location(1,1));
        $var->getValue();
    }

    public function testGetValueReturnsDefaultValueIfNoValueSet(): void
    {
        $var = new Variable('foo', 'bar', false, false, true, new Location(1,1));
        $var->setDefaultValue('default-value');

        $this->assertEquals(
            'default-value',
            $var->getValue()
        );
    }

    public function testGetValueReturnsSetValueEvenWithDefaultValue(): void
    {
        $var = new Variable('foo', 'bar', false, false, true, new Location(1,1));
        $var->setValue('real-value');
        $var->setDefaultValue('default-value');

        $this->assertEquals(
            'real-value',
            $var->getValue()
        );
    }

    public function testIndicatesDefaultValuePresent(): void
    {
        $var = new Variable('foo', 'bar', false, false, true, new Location(1,1));
        $var->setDefaultValue('default-value');

        $this->assertTrue(
            $var->hasDefaultValue()
        );
    }

    public function testHasNoDefaultValue(): void
    {
        $var = new Variable('foo', 'bar', false, false, true, new Location(1,1));

        $this->assertFalse(
            $var->hasDefaultValue()
        );
    }

    /**
     * @return array Array of <mixed: value to set, mixed: expected value>
     */
    public static function variableProvider()
    {
        return [
            [
                0,
                0
            ]
        ];
    }
}
