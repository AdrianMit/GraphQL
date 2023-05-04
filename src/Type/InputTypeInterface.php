<?php

namespace Dreamlabs\GraphQL\Type;


use Dreamlabs\GraphQL\Config\AbstractConfig;

interface InputTypeInterface
{
    /**
     * @return string|null type name
     */
    public function getName(): ?string;

    /**
     * @return String predefined type kind
     */
    public function getKind(): string;

    /**
     * @return String type description
     */
    public function getDescription(): string;
    
    /**
     * Coercing value received as input to current type
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function parseValue(mixed $value): mixed;
    
    /**
     * Coercing result to current type
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function serialize(mixed $value): mixed;

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isValidValue(mixed $value): bool;

    /**
     * @return AbstractConfig
     */
    public function getConfig(): AbstractConfig;
}
