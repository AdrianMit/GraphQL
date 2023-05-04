<?php

namespace Dreamlabs\GraphQL\Type\ListType;


use Dreamlabs\GraphQL\Config\AbstractConfig;
use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;
use Dreamlabs\GraphQL\Exception\ConfigurationException;
use Traversable;
use Dreamlabs\GraphQL\Config\Object\ListTypeConfig;
use Dreamlabs\GraphQL\Type\CompositeTypeInterface;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\TypeMap;

abstract class AbstractListType extends AbstractObjectType implements CompositeTypeInterface
{
    protected AbstractConfig $config;
    
    /**
     * @throws ConfigurationException
     */
    public function __construct()
    {
        $this->config = new ListTypeConfig(['itemType' => $this->getItemType()], $this);
    }

    /**
     * @return AbstractObjectType
     */
    abstract public function getItemType();

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isValidValue(mixed $value): bool
    {
        if (!$this->isIterable($value)) {
            return false;
        }

        return $this->validList($value);
    }

    /**
     * @param $value
     * @param bool $returnValue
     *
     * @return bool
     */
    protected function validList($value, $returnValue = false)
    {
        $itemType = $this->config->get('itemType');

        if ($value && $itemType->isInputType()) {
            foreach ($value as $item) {
                if (!$itemType->isValidValue($item)) {
                    return $returnValue ? $item : false;
                }
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function build(ObjectTypeConfig $config): void
    {
    }

    public function isCompositeType()
    {
        return true;
    }

    public function getNamedType()
    {
        return $this->getItemType();
    }

    final public function getKind(): string
    {
        return TypeMap::KIND_LIST;
    }

    public function getTypeOf()
    {
        return $this->getNamedType();
    }

    public function parseValue($value): mixed
    {
        foreach ((array) $value as $keyValue => $valueItem) {
            $value[$keyValue] = $this->getItemType()->parseValue($valueItem);
        }

        return $value;
    }

    public function getValidationError($value = null)
    {
        if (!$this->isIterable($value)) {
            return 'The value is not an iterable.';
        }
        return $this->config->get('itemType')->getValidationError($this->validList($value, true));
    }

    /**
     * @param $value
     *
     * @return bool
     */
    protected function isIterable($value)
    {
        return null === $value || is_array($value) || ($value instanceof Traversable);
    }
}
