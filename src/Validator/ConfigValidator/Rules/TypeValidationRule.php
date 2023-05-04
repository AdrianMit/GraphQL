<?php

namespace Dreamlabs\GraphQL\Validator\ConfigValidator\Rules;


use Dreamlabs\GraphQL\Field\FieldInterface;
use Dreamlabs\GraphQL\Field\InputFieldInterface;
use Dreamlabs\GraphQL\Type\AbstractType;
use Dreamlabs\GraphQL\Type\TypeFactory;
use Dreamlabs\GraphQL\Type\TypeService;
use Dreamlabs\GraphQL\Validator\ConfigValidator\ConfigValidator;

class TypeValidationRule implements ValidationRuleInterface
{

    public function __construct(private ConfigValidator $configValidator)
    {
    }

    public function validate($data, $ruleInfo)
    {
        if (!is_string($ruleInfo)) return false;

        return match ($ruleInfo) {
            TypeService::TYPE_ANY => true,
            TypeService::TYPE_ANY_OBJECT => is_object($data),
            TypeService::TYPE_CALLABLE => is_callable($data),
            TypeService::TYPE_BOOLEAN => is_bool($data),
            TypeService::TYPE_ARRAY => is_array($data),
            TypeService::TYPE_STRING => TypeFactory::getScalarType($ruleInfo)->isValidValue($data),
            TypeService::TYPE_GRAPHQL_TYPE => TypeService::isGraphQLType($data),
            TypeService::TYPE_OBJECT_TYPE => TypeService::isObjectType($data),
            TypeService::TYPE_ARRAY_OF_OBJECT_TYPES => $this->isArrayOfObjectTypes($data),
            TypeService::TYPE_ARRAY_OF_FIELDS_CONFIG => $this->isArrayOfFields($data),
            TypeService::TYPE_OBJECT_INPUT_TYPE => TypeService::isInputObjectType($data),
            TypeService::TYPE_ENUM_VALUES => $this->isEnumValues($data),
            TypeService::TYPE_ARRAY_OF_INPUT_FIELDS => $this->isArrayOfInputFields($data),
            TypeService::TYPE_ANY_INPUT => TypeService::isInputType($data),
            TypeService::TYPE_ARRAY_OF_INTERFACES => self::isArrayOfInterfaces($data),
            default => false,
        };
    }

    private function isArrayOfObjectTypes($data)
    {
        if (!is_array($data) || !count($data)) {
            return false;
        }

        foreach ($data as $item) {
            if (!TypeService::isObjectType($item)) {
                return false;
            }
        }

        return true;
    }

    private function isEnumValues($data)
    {
        if (!is_array($data) || empty($data)) return false;

        foreach ($data as $item) {
            if (!is_array($item) || !array_key_exists('name', $item) || !is_string($item['name']) || !preg_match('/^[_a-zA-Z][_a-zA-Z0-9]*$/', $item['name'])) {
                return false;
            }

            if (!array_key_exists('value', $item)) {
                return false;
            }
        }

        return true;
    }

    private static function isArrayOfInterfaces($data)
    {
        if (!is_array($data)) return false;

        foreach ($data as $item) {
            if (!TypeService::isInterface($item)) {
                return false;
            }
        }

        return true;
    }

    private function isArrayOfFields($data)
    {
        if (!is_array($data) || empty($data)) return false;

        foreach ($data as $name => $item) {
            if (!$this->isField($item, $name)) return false;
        }

        return true;
    }

    private function isField($data, $name = null)
    {
        if (is_object($data)) {
            if (($data instanceof FieldInterface) || ($data instanceof AbstractType)) {
                return !$data->getConfig() || ($data->getConfig() && $this->configValidator->isValidConfig($data->getConfig()));
            } else {
                return false;
            }
        }
        if (!is_array($data)) {
            $data = [
                'type' => $data,
                'name' => $name,
            ];
        } elseif (empty($data['name'])) {
            $data['name'] = $name;
        }
        $this->configValidator->validate($data, $this->getFieldConfigRules());

        return $this->configValidator->isValid();
    }

    private function isArrayOfInputFields($data)
    {
        if (!is_array($data)) return false;

        foreach ($data as $name => $item) {
            if (!$this->isInputField($item)) return false;
        }

        return true;
    }

    private function isInputField($data)
    {
        if (is_object($data)) {
            if ($data instanceof InputFieldInterface) {
                return true;
            } else {
                return TypeService::isInputType($data);
            }
        } else {
            if (!isset($data['type'])) {
                return false;
            }

            return TypeService::isInputType($data['type']);
        }
    }

    /**
     * Exists for the performance
     * @return array
     */
    private function getFieldConfigRules()
    {
        return [
            'name'              => ['type' => TypeService::TYPE_STRING, 'required' => true],
            'type'              => ['type' => TypeService::TYPE_ANY, 'required' => true],
            'args'              => ['type' => TypeService::TYPE_ARRAY],
            'description'       => ['type' => TypeService::TYPE_STRING],
            'resolve'           => ['type' => TypeService::TYPE_CALLABLE],
            'isDeprecated'      => ['type' => TypeService::TYPE_BOOLEAN],
            'deprecationReason' => ['type' => TypeService::TYPE_STRING],
            'cost'              => ['type' => TypeService::TYPE_ANY]
        ];
    }

}
