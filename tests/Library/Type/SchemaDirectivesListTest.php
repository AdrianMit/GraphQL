<?php

namespace Dreamlabs\Tests\Library\Type;

use PHPUnit\Framework\TestCase;
use Exception;
use Dreamlabs\GraphQL\Directive\Directive;
use Dreamlabs\GraphQL\Type\SchemaDirectivesList;

class SchemaDirectivesListTest extends TestCase
{
    public function testCanAddASingleDirective(): void
    {
        $directiveList = new SchemaDirectivesList();
        $directiveList->addDirective(
            new Directive([
                'name' => 'testDirective'
            ])
        );
        $this->assertTrue($directiveList->isDirectiveNameRegistered('testDirective'));
    }

    public function testCanAddMultipleDirectives(): void
    {
        $directiveList = new SchemaDirectivesList();
        $directiveList->addDirectives([
            new Directive([
                'name' => 'testDirectiveOne'
            ]),
            new Directive([
                'name' => 'testDirectiveTwo'
            ]),
        ]);
        $this->assertTrue($directiveList->isDirectiveNameRegistered('testDirectiveOne'));
        $this->assertTrue($directiveList->isDirectiveNameRegistered('testDirectiveTwo'));
    }

    public function testItThrowsExceptionWhenAddingInvalidDirectives(): void
    {
        $this->setExpectedException(Exception::class, "addDirectives accept only array of directives");
        $directiveList = new SchemaDirectivesList();
        $directiveList->addDirectives("foobar");
    }

}
