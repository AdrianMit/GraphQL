<?php

namespace Dreamlabs\Tests\Library\Utilities;

use PHPUnit\Framework\TestCase;
use Exception;
use Dreamlabs\GraphQL\Exception\Interfaces\ExtendedExceptionInterface;
use Dreamlabs\GraphQL\Exception\Interfaces\LocationableExceptionInterface;
use Dreamlabs\GraphQL\Exception\Parser\SyntaxErrorException;
use Dreamlabs\GraphQL\Parser\Location;
use Dreamlabs\GraphQL\Validator\ErrorContainer\ErrorContainerInterface;
use Dreamlabs\GraphQL\Validator\ErrorContainer\ErrorContainerTrait;

class ErrorContainerTraitTest extends TestCase implements ErrorContainerInterface
{

    use ErrorContainerTrait;

    protected function setUp(): void
    {
        $this->clearErrors();
    }

    public function testAddHasClearMergeErrors(): void
    {
        $error = new Exception('Error');
        $this->addError($error);
        $this->assertTrue($this->hasErrors());

        $this->clearErrors();
        $this->assertFalse($this->hasErrors());

        $this->addError($error);
        $this->assertEquals([$error], $this->getErrors());

        $this->mergeErrors($this);
        $this->assertEquals([$error, $error], $this->getErrors());
    }

    public function testGetErrorsAsArrayGenericExceptionWithoutCode(): void
    {
        // Code is zero by default
        $this->addError(new Exception('Generic exception'));
        $this->assertEquals([
            [
                'message' => 'Generic exception',
            ],
        ], $this->getErrorsArray());
    }

    public function testGetErrorsAsArrayGenericExceptionWithCode(): void
    {
        $this->addError(new Exception('Generic exception with code', 4));
        $this->assertEquals([
            [
                'message' => 'Generic exception with code',
                'code'    => 4,
            ],
        ], $this->getErrorsArray());
    }

    public function testGetErrorsAsArrayLocationableException(): void
    {
        $this->addError(new SyntaxErrorException('Syntax error', new Location(5, 88)));
        $this->assertEquals([
            [
                'message'   => 'Syntax error',
                'locations' => [
                    [
                        'line'   => 5,
                        'column' => 88,
                    ],
                ],
            ],
        ], $this->getErrorsArray());
    }

    public function testGetErrorsAsArrayExtendedException(): void
    {
        $this->addError(new ExtendedException('Extended exception'));
        $this->assertEquals([
            [
                'message'    => 'Extended exception',
                'extensions' => [
                    'foo' => 'foo',
                    'bar' => 'bar',
                ],
            ],
        ], $this->getErrorsArray());
    }

    public function testGetErrorsAsArrayExceptionWithEverything(): void
    {
        $this->addError(new SuperException('Super exception', 3));
        $this->assertEquals([
            [
                'message'    => 'Super exception',
                'code'       => 3,
                'locations'  => [
                    [
                        'line'   => 6,
                        'column' => 10,
                    ],
                ],
                'extensions' => [
                    'foo' => 'foo',
                    'bar' => 'bar',
                ],
            ],
        ], $this->getErrorsArray());
    }
}

class ExtendedException extends Exception implements ExtendedExceptionInterface
{
    public function getExtensions(): array
    {
        return [
            'foo' => 'foo',
            'bar' => 'bar',
        ];
    }
}

class SuperException extends Exception implements LocationableExceptionInterface, ExtendedExceptionInterface
{
    public function getExtensions(): array
    {
        return [
            'foo' => 'foo',
            'bar' => 'bar',
        ];
    }

    public function getLocation(): Location
    {
        return new Location(6, 10);
    }
}
