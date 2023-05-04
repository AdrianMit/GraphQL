<?php

namespace Dreamlabs\Tests\Issues\Issue220;

use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;
use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Field\Field;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;
use Dreamlabs\Tests\DataProvider\TestResolveInfo;

class Issue220Test extends TestCase
{

    public function testValueNotFoundInResolveScalarType(): void
    {
        $fieldWithResolve = new Field([
            'name' => 'scalarField',
            'type' => new StringType(),
        ]);

        $resolveInfo = TestResolveInfo::createTestResolveInfo($fieldWithResolve);

        $this->assertEquals(null, $fieldWithResolve->resolve([], [], $resolveInfo));
    }

    public function testValueNotFoundInResolveObjectType(): void
    {
        $fieldWithResolve = new Field([
            'name' => 'scalarField',
            'type' => new ArticleType(),
        ]);

        $resolveInfo = TestResolveInfo::createTestResolveInfo($fieldWithResolve);

        $this->assertEquals(null, $fieldWithResolve->resolve([], [], $resolveInfo));
    }

    public function testValueFoundInResolve(): void
    {
        $fieldWithResolve = new Field([
            'name' => 'scalarField',
            'type' => new StringType(),
        ]);

        $resolveInfo = TestResolveInfo::createTestResolveInfo($fieldWithResolve);

        $this->assertEquals('foo', $fieldWithResolve->resolve(['scalarField' => 'foo'], [], $resolveInfo));
    }
}

class ArticleType extends AbstractObjectType
{
    public function build(ObjectTypeConfig $config): void
    {
        $config->addFields([
            'title' => new StringType(),
        ]);
    }
}
