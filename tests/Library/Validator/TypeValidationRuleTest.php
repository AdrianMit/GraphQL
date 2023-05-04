<?php

namespace Dreamlabs\Tests\Library\Validator;


use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Field\Field;
use Dreamlabs\GraphQL\Type\Scalar\StringType;
use Dreamlabs\GraphQL\Type\TypeMap;
use Dreamlabs\GraphQL\Type\TypeService;
use Dreamlabs\GraphQL\Validator\ConfigValidator\ConfigValidator;
use Dreamlabs\GraphQL\Validator\ConfigValidator\Rules\TypeValidationRule;
use Dreamlabs\Tests\DataProvider\TestInputField;
use Dreamlabs\Tests\DataProvider\TestInputObjectType;
use Dreamlabs\Tests\DataProvider\TestObjectType;

class TypeValidationRuleTest extends TestCase
{

    /**
     * @var TypeValidationRule
     */
    protected $rule;

    protected function setUp(): void
    {
        $this->rule = new TypeValidationRule(ConfigValidator::getInstance());
    }


    /**
     * @param      $ruleInfo
     * @param      $data
     * @param bool $isValid
     *
     * @dataProvider simpleRulesProvider
     */
    public function testSimpleRules($ruleInfo, $data, $isValid = true): void
    {
        $this->assertEquals($isValid, $this->rule->validate($data, $ruleInfo));
    }

    public function simpleRulesProvider()
    {
        return [
            [TypeService::TYPE_ARRAY_OF_FIELDS_CONFIG, ["fieldName" => new StringType()]],

            [TypeService::TYPE_ANY, null],

            [TypeService::TYPE_ANY_OBJECT, new StringType()],
            [TypeService::TYPE_ANY_OBJECT, null, false],

            [TypeService::TYPE_CALLABLE, function (): void { }],
            [TypeService::TYPE_CALLABLE, null, false],

            [TypeService::TYPE_BOOLEAN, true],
            [TypeService::TYPE_BOOLEAN, false],
            [TypeService::TYPE_BOOLEAN, null, false],

            [TypeService::TYPE_ARRAY, []],
            [TypeService::TYPE_ARRAY, null, false],

            [TypeService::TYPE_OBJECT_TYPE, new TestObjectType()],
            [TypeService::TYPE_OBJECT_TYPE, new StringType(), false],

            [TypeService::TYPE_ARRAY_OF_FIELDS_CONFIG, ["fieldName" => TypeMap::TYPE_STRING]],
            [TypeService::TYPE_ARRAY_OF_FIELDS_CONFIG, [new Field(['name' => 'id', 'type' => new StringType()])]],
            [TypeService::TYPE_ARRAY_OF_FIELDS_CONFIG, [], false],

            [null, null, false],
            ['invalid rule', null, false],
        ];
    }

    /**
     * @param      $ruleInfo
     * @param      $data
     * @param bool $isValid
     *
     * @dataProvider complexRuleProvider
     */
    public function testComplexRules($ruleInfo, $data, $isValid = true): void
    {
        $this->assertEquals($isValid, $this->rule->validate($data, $ruleInfo));
    }

    public static function complexRuleProvider()
    {
        return [
            [TypeService::TYPE_OBJECT_INPUT_TYPE, new TestInputObjectType()],
            [TypeService::TYPE_OBJECT_INPUT_TYPE, new StringType(), false],

            [TypeService::TYPE_ARRAY_OF_INPUT_FIELDS, [new TestInputObjectType(), new TestInputField()]],
            [TypeService::TYPE_ARRAY_OF_INPUT_FIELDS, [new StringType()]],
            [TypeService::TYPE_ARRAY_OF_INPUT_FIELDS, [['type' => TypeMap::TYPE_STRING]]],
            [TypeService::TYPE_ARRAY_OF_INPUT_FIELDS, [[]], false],
            [TypeService::TYPE_ARRAY_OF_INPUT_FIELDS, new StringType(), false],

            [TypeService::TYPE_ARRAY_OF_OBJECT_TYPES, [new TestObjectType()]],
            [TypeService::TYPE_ARRAY_OF_OBJECT_TYPES, [], false],

        ];
    }

}
