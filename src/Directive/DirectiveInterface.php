<?php

namespace Dreamlabs\GraphQL\Directive;


use Dreamlabs\GraphQL\Field\InputField;

interface DirectiveInterface
{
    public function getName();
    public function addArguments(array $argumentsList);
    public function removeArgument(string $argumentName);
    public function addArgument($argument, $ArgumentInfo = null);
    public function getArguments(): array;
    public function getArgument(string $argumentName): ?InputField;
    public function hasArgument(string $argumentName): bool;
    public function hasArguments(): bool;

}
