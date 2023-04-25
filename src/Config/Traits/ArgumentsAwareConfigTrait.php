<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/1/15 11:07 PM
*/

namespace Youshido\GraphQL\Config\Traits;


use Youshido\GraphQL\Field\InputField;

trait ArgumentsAwareConfigTrait
{
    protected array $arguments = [];
    protected bool $_isArgumentsBuilt;

    public function buildArguments(): void
    {
        if ($this->_isArgumentsBuilt) {
            return;
        }

        if (!empty($this->data['args'])) {
            $this->addArguments($this->data['args']);
        }
        $this->_isArgumentsBuilt = true;
    }

    public function addArguments(array $argsList): static
    {
        foreach ($argsList as $argumentName => $argumentInfo) {
            if ($argumentInfo instanceof InputField) {
                $this->arguments[$argumentInfo->getName()] = $argumentInfo;
                continue;
            } else {
                $this->addArgument($argumentName, $this->buildConfig($argumentName, $argumentInfo));
            }
        }

        return $this;
    }

    public function addArgument(object $argument, mixed $argumentInfo = null): static
    {
        if (!($argument instanceof InputField)) {
            $argument = new InputField($this->buildConfig($argument, $argumentInfo));
        }
        $this->arguments[$argument->getName()] = $argument;

        return $this;
    }

    protected function buildConfig(mixed $name, mixed $info = null): array
    {
        if (!is_array($info)) {
            return [
                'type' => $info,
                'name' => $name
            ];
        }
        if (empty($info['name'])) {
            $info['name'] = $name;
        }

        return $info;
    }

    public function getArgument(string $name): ?InputField
    {
        return $this->hasArgument($name) ? $this->arguments[$name] : null;
    }

    public function hasArgument(string $name): bool
    {
        return array_key_exists($name, $this->arguments);
    }

    public function hasArguments(): bool
    {
        return !empty($this->arguments);
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function removeArgument(string $name): static
    {
        if ($this->hasArgument($name)) {
            unset($this->arguments[$name]);
        }

        return $this;
    }
}
