<?php

namespace Dreamlabs\Tests\Library\Config;


use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;
use Dreamlabs\GraphQL\Validator\ConfigValidator\ConfigValidator;
use Dreamlabs\Tests\DataProvider\TestInterfaceType;

class ObjectTypeConfigTest extends TestCase
{

    public function testCreation(): void
    {
        $config = new ObjectTypeConfig(['name' => 'Test'], null, false);
        $this->assertEquals($config->getName(), 'Test', 'Normal creation');
    }

    /**
     * @expectedException Dreamlabs\GraphQL\Exception\ConfigurationException
     */
    public function testInvalidConfigNoFields(): void
    {
        ConfigValidator::getInstance()->assertValidConfig(
            new ObjectTypeConfig(['name' => 'Test'], null, true)
        );
    }

    /**
     * @expectedException Dreamlabs\GraphQL\Exception\ConfigurationException
     */
    public function testInvalidConfigInvalidInterface(): void
    {
        ConfigValidator::getInstance()->assertValidConfig(
            new ObjectTypeConfig(['name' => 'Test', 'interfaces' => ['Invalid interface']], null, false)
        );
    }

    public function testInterfaces(): void
    {
        $testInterfaceType = new TestInterfaceType();
        $config            = new ObjectTypeConfig(['name' => 'Test', 'interfaces' => [$testInterfaceType]], null, false);
        $this->assertEquals($config->getInterfaces(), [$testInterfaceType]);
    }


}
