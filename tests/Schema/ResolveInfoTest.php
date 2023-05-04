<?php

namespace Dreamlabs\Tests\Schema;


use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Execution\Context\ExecutionContext;
use Dreamlabs\GraphQL\Execution\ResolveInfo;
use Dreamlabs\GraphQL\Field\Field;
use Dreamlabs\GraphQL\Parser\Ast\Field as FieldAST;
use Dreamlabs\GraphQL\Parser\Location;
use Dreamlabs\GraphQL\Type\Scalar\IntType;
use Dreamlabs\Tests\DataProvider\TestSchema;

class ResolveInfoTest extends TestCase
{
    public function testMethods(): void
    {
        $fieldAst         = new FieldAST('name', null, [], [], new Location(1,1));
        $field            = new Field(['name' => 'id', 'type' => new IntType()]);
        $returnType       = new IntType();
        $executionContext = new ExecutionContext(new TestSchema());
        $info             = new ResolveInfo($field, [$fieldAst], $executionContext);

        $this->assertEquals($field, $info->getField());
        $this->assertEquals([$fieldAst], $info->getFieldASTList());
        $this->assertEquals($returnType, $info->getReturnType());
        $this->assertEquals($executionContext, $info->getExecutionContext());
    }
}
