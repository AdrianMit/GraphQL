<?php
/**
 * Date: 3/17/17
 *
 * @author Volodymyr Rashchepkin <rashepkin@gmail.com>
 */

namespace Youshido\GraphQL\Directive;


use Youshido\GraphQL\Field\InputField;

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
