<?php

namespace Dreamlabs\Tests\Library\Config;


use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Config\Field\FieldConfig;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class FieldConfigTest extends TestCase
{

    public function testInvalidParams(): void
    {
        $fieldConfig = new FieldConfig([
            'name'    => 'FirstName',
            'type'    => new StringType(),
            'resolve' => fn($value, $args = [], $type = null): string => 'John'
        ]);

        $this->assertEquals('FirstName', $fieldConfig->getName());
        $this->assertEquals(new StringType(), $fieldConfig->getType());

        $resolveFunction = $fieldConfig->getResolveFunction();
        $this->assertEquals('John', $resolveFunction([]));
    }

}
