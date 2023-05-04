<?php

namespace Dreamlabs\GraphQL\Parser\Ast\ArgumentValue;

use LogicException;
use Dreamlabs\GraphQL\Parser\Ast\AbstractAst;
use Dreamlabs\GraphQL\Parser\Ast\Interfaces\ValueInterface;
use Dreamlabs\GraphQL\Parser\Location;

class Variable extends AbstractAst implements ValueInterface
{

    /** @var  mixed */
    private $value;

    private bool $used = false;

    private bool $hasDefaultValue = false;

    /** @var mixed */
    private $defaultValue = null;

    /**
     * @param string   $name
     * @param string   $type
     * @param bool     $nullable
     * @param bool     $isArray
     * @param bool     $arrayElementNullable
     * @param Location $location
     */
    public function __construct(private $name, private $type, private $nullable, private $isArray, Location $location, private bool $arrayElementNullable = true)
    {
        parent::__construct($location);
    }

    /**
     * @return mixed
     *
     * @throws \LogicException
     */
    public function getValue()
    {
        if (null === $this->value) {
            if ($this->hasDefaultValue()) {
                return $this->defaultValue;
            }
            throw new LogicException('Value is not set for variable "' . $this->name . '"');
        }

        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getTypeName()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setTypeName($type): void
    {
        $this->type = $type;
    }

    /**
     * @return boolean
     */
    public function isArray()
    {
        return $this->isArray;
    }

    /**
     * @param boolean $isArray
     */
    public function setIsArray($isArray): void
    {
        $this->isArray = $isArray;
    }

    /**
     * @return boolean
     */
    public function isNullable()
    {
        return $this->nullable;
    }

    /**
     * @param boolean $nullable
     */
    public function setNullable($nullable): void
    {
        $this->nullable = $nullable;
    }

    public function hasDefaultValue(): bool
    {
        return $this->hasDefaultValue;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function setDefaultValue(mixed $defaultValue): void
    {
        $this->hasDefaultValue = true;

        $this->defaultValue = $defaultValue;
    }

    public function isUsed(): bool
    {
        return $this->used;
    }

    /**
     * @param boolean $used
     *
     * @return $this
     */
    public function setUsed($used)
    {
        $this->used = $used;

        return $this;
    }

    /**
     * @return bool
     */
    public function isArrayElementNullable()
    {
        return $this->arrayElementNullable;
    }

    /**
     * @param bool $arrayElementNullable
     */
    public function setArrayElementNullable($arrayElementNullable): void
    {
        $this->arrayElementNullable = $arrayElementNullable;
    }
}
