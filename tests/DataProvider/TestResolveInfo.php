<?php

namespace Dreamlabs\Tests\DataProvider;


use Dreamlabs\GraphQL\Execution\Context\ExecutionContext;
use Dreamlabs\GraphQL\Execution\ResolveInfo;

class TestResolveInfo
{
    public static function createTestResolveInfo($field = null): ResolveInfo
    {
        if (empty($field)) {
            $field = new TestField();
        }

        return new ResolveInfo($field, [], new ExecutionContext(new TestSchema()));
    }
}
