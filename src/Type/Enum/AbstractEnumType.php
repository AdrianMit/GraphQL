<?php

namespace Dreamlabs\GraphQL\Type\Enum;


use Dreamlabs\GraphQL\Config\Object\EnumTypeConfig;
use Dreamlabs\GraphQL\Config\Traits\ConfigAwareTrait;
use Dreamlabs\GraphQL\Type\AbstractType;
use Dreamlabs\GraphQL\Type\Traits\AutoNameTrait;
use Dreamlabs\GraphQL\Type\TypeMap;

abstract class AbstractEnumType extends AbstractType
{

    use AutoNameTrait, ConfigAwareTrait;

    /**
     * ObjectType constructor.
     * @param $config
     */
    public function __construct($config = [])
    {
        if (empty($config)) {
            $config['name']   = $this->getName();
            $config['values'] = $this->getValues();
        }

        $this->config = new EnumTypeConfig($config, $this);
    }

    /**
     * @return String predefined type kind
     */
    public function getKind(): string
    {
        return TypeMap::KIND_ENUM;
    }

    /**
     * @param $value mixed
     *
     * @return bool
     */
    public function isValidValue(mixed $value): bool
    {
        if (is_null($value)) return true;
        foreach ($this->getConfig()->get('values') as $item) {
            if ($value === $item['name'] || $value === $item['value']) {
                return true;
            }
        }

        return false;
    }

    public function getValidationError($value = null)
    {
        $allowedValues             = array_map(fn(array $value): string => sprintf('%s (%s)', $value['name'], $value['value']), $this->getConfig()->get('values'));
        return sprintf('Value must be one of the allowed ones: %s', implode(', ', $allowedValues));
    }

    /**
     * @return array
     */
    abstract public function getValues();

    public function serialize($value): mixed
    {
        foreach ($this->getConfig()->get('values') as $valueItem) {
            if ($value === $valueItem['value']) {
                return $valueItem['name'];
            }
        }

        return null;
    }

    public function parseValue($value): mixed
    {
        foreach ($this->getConfig()->get('values') as $valueItem) {
            if ($value === $valueItem['name']) {
                return $valueItem['value'];
            }
        }

        return null;
    }

    public function parseInputValue($value)
    {
        foreach ($this->getConfig()->get('values') as $valueItem) {
            if ($value === $valueItem['value']) {
                return $valueItem['name'];
            }
        }

        return null;
    }

}
