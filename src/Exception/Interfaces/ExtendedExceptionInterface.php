<?php

namespace Youshido\GraphQL\Exception\Interfaces;

/**
 * Interface for GraphQL exceptions that have "extensions" defined
 */
interface ExtendedExceptionInterface
{
    public function getExtensions(): array;
}
