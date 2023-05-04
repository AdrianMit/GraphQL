<?php

namespace Dreamlabs\Tests\Library\Config;

use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Type\Enum\EnumType;
use Dreamlabs\GraphQL\Type\Object\ObjectType;
use Dreamlabs\GraphQL\Type\Scalar\IdType;
use Dreamlabs\GraphQL\Type\Scalar\IntType;
use Dreamlabs\GraphQL\Type\TypeService;
use Dreamlabs\GraphQL\Validator\ConfigValidator\ConfigValidator;
use Dreamlabs\Tests\DataProvider\TestConfig;
use Dreamlabs\Tests\DataProvider\TestConfigExtraFields;
use Dreamlabs\Tests\DataProvider\TestConfigInvalidRule;

class ConfigTest extends TestCase
{

    /**
     * @expectedException Dreamlabs\GraphQL\Exception\ConfigurationException
     */
    public function testEmptyParams(): void
    {
        new TestConfig([]);
    }

    /**
     * @expectedException Dreamlabs\GraphQL\Exception\ConfigurationException
     */
    public function testInvalidParams(): void
    {
        ConfigValidator::getInstance()->assertValidConfig(new TestConfig(['id' => 1]));
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidMethod(): void
    {
        $config = new TestConfig(['name' => 'test']);
        $config->doSomethingStrange();
    }

    public function testMethods(): void
    {
        $name  = 'Test';
        $rules = [
            'name'    => ['type' => TypeService::TYPE_ANY, 'required' => true],
            'resolve' => ['type' => TypeService::TYPE_CALLABLE, 'final' => true],
        ];

        $config = new TestConfig(['name' => $name]);
        $this->assertEquals($config->getName(), $name);
        $this->assertEquals($config->get('name'), $name);
        $this->assertEquals($config->get('non existing key'), null);
        $this->assertEquals($config->set('name', 'StrangeName'), $config);
        $this->assertEquals($config->get('name'), 'StrangeName');
        $this->assertEquals($config->get('non existing', 'default'), 'default');
        $this->assertEquals($config->isName(), 'StrangeName');
        $this->assertEquals($config->setName('StrangeName 2'), $config);

        $config->set('var', 'value');
        $this->assertEquals($config->getVar(), 'value');

        $this->assertEquals($config->getRules(), $rules);
        $this->assertEquals($config->getContextRules(), $rules);
        $this->assertNull($config->getResolveFunction());

        $object = new ObjectType([
            'name'   => 'TestObject',
            'fields' => [
                'id' => [
                    'type' => new IntType()
                ]
            ]
        ]);

        $finalConfig = new TestConfig(['name' => $name . 'final', 'resolve' => fn(): array => []], $object, true);
        $this->assertEquals($finalConfig->getType(), null);

        $rules['resolve']['required'] = true;
        $this->assertEquals($finalConfig->getContextRules(), $rules);

        $this->assertNotNull($finalConfig->getResolveFunction());

        $configExtraFields = new TestConfigExtraFields([
            'name'       => 'Test',
            'extraField' => 'extraValue'
        ]);
        $this->assertEquals('extraValue', $configExtraFields->get('extraField'));
    }

    /**
     * @expectedException Dreamlabs\GraphQL\Exception\ConfigurationException
     */
    public function testFinalRule(): void
    {
        ConfigValidator::getInstance()->assertValidConfig(new TestConfig(['name' => 'Test' . 'final'], null, true));
    }

    /**
     * @expectedException Dreamlabs\GraphQL\Exception\ConfigurationException
     */
    public function testInvalidRule(): void
    {
        ConfigValidator::getInstance()->assertValidConfig(
            new TestConfigInvalidRule(['name' => 'Test', 'invalidRuleField' => 'test'], null, null)
        );
    }

    /**
     * @expectedException Dreamlabs\GraphQL\Exception\ConfigurationException
     */
    public function testEnumConfig(): void
    {
        $enumType = new EnumType([
            'name'   => 'Status',
            'values' => [
                [
                    'name'   => 'ACTIVE',
                    'values' => 1
                ]
            ]
        ]);
        $object   = new ObjectType([
            'name' => 'Project',
            'fields' => [
                'id' => new IdType(),
                'status' => $enumType
            ]
        ]);
        ConfigValidator::getInstance()->assertValidConfig($object->getConfig());
    }

}
