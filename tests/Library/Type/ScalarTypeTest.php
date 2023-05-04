<?php

namespace Dreamlabs\Tests\Library\Type;

use PHPUnit\Framework\TestCase;
use Dreamlabs\Tests\DataProvider\TestScalarDataProvider;
use Exception;
use DateTimeImmutable;
use DateTime;
use Dreamlabs\GraphQL\Type\Scalar\AbstractScalarType;
use Dreamlabs\GraphQL\Type\Scalar\DateTimeType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;
use Dreamlabs\GraphQL\Type\TypeFactory;
use Dreamlabs\GraphQL\Type\TypeMap;
use Dreamlabs\GraphQL\Type\TypeService;

class ScalarTypeTest extends TestCase
{

    public function testScalarPrimitives(): void
    {
        foreach (TypeFactory::getScalarTypesNames() as $typeName) {
            $scalarType     = TypeFactory::getScalarType($typeName);
            $testDataMethod = 'get' . $typeName . 'TestData';

            $this->assertNotEmpty($scalarType->getDescription());
            $this->assertEquals($scalarType->getKind(), TypeMap::KIND_SCALAR);
            $this->assertEquals($scalarType->isCompositeType(), false);
            $this->assertEquals(TypeService::isAbstractType($scalarType), false);
            $this->assertEquals($scalarType->getType(), $scalarType);
            $this->assertEquals($scalarType->getType(), $scalarType->getNamedType());
            $this->assertNull($scalarType->getConfig());

            foreach (call_user_func([TestScalarDataProvider::class, $testDataMethod]) as [$data, $serialized, $isValid]) {

                $this->assertSerialization($scalarType, $data, $serialized);
                $this->assertParse($scalarType, $data, $serialized, $typeName);

                if ($isValid) {
                    $this->assertTrue($scalarType->isValidValue($data), $typeName . ' validation for :' . serialize($data));
                } else {
                    $this->assertFalse($scalarType->isValidValue($data), $typeName . ' validation for :' . serialize($data));
                }
            }
        }
        try {
            TypeFactory::getScalarType('invalid type');
        } catch (Exception $e) {
            $this->assertEquals('Configuration problem with type invalid type', $e->getMessage());
        }
        $this->assertEquals('String', (string)new StringType());

    }

    public function testDateTimeType(): void
    {
        $dateType = new DateTimeType('Y/m/d H:i:s');
        $this->assertEquals('2016/05/31 12:00:00', $dateType->serialize(new DateTimeImmutable('2016-05-31 12:00pm')));
    }

    private function assertSerialization(AbstractScalarType $object, $input, $expected): void
    {
        $this->assertEquals($expected, $object->serialize($input), $object->getName() . ' serialize for: ' . serialize($input));
    }

    private function assertParse(AbstractScalarType $object, $input, $expected, $typeName): void
    {
        $parsed = $object->parseValue($input);
        if ($parsed instanceof DateTime) {
            $expected = DateTime::createFromFormat($typeName === 'datetime' ? 'Y-m-d H:i:s' : 'D, d M Y H:i:s O', $expected);
            $parsed   = DateTime::createFromFormat('Y-m-d H:i:s', $parsed->format('Y-m-d H:i:s'));
        }

        $this->assertEquals($expected, $parsed, $object->getName() . ' parse for: ' . serialize($input));
    }
}
