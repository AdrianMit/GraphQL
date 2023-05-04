<?php


namespace Dreamlabs\GraphQL\Field;


use Dreamlabs\GraphQL\Type\AbstractType;

interface InputFieldInterface
{
    public function getType(): AbstractType;

    public function getName();

    public function addArguments($argumentsList);

    public function removeArgument($argumentName);

    public function addArgument($argument, $ArgumentInfo = null);

    public function getArguments(): array;

    public function getArgument(string $argumentName): AbstractType|InputFieldInterface;

    public function hasArgument(string $argumentName): bool;
    
    public function hasArguments(): bool;


}
