<?php

namespace Dreamlabs\GraphQL\Type\Traits;


use Dreamlabs\GraphQL\Field\InputFieldInterface;
use Dreamlabs\GraphQL\Type\AbstractType;

trait FieldsArgumentsAwareObjectTrait
{
    use FieldsAwareObjectTrait;

    protected $hasArgumentCache = null;

    public function addArguments($argumentsList)
    {
        return $this->getConfig()->addArguments($argumentsList);
    }

    public function removeArgument($argumentName)
    {
        return $this->getConfig()->removeArgument($argumentName);
    }

    public function addArgument($argument, $ArgumentInfo = null)
    {
        return $this->getConfig()->addArgument($argument, $ArgumentInfo);
    }
    
    public function getArguments(): array
    {
        return $this->getConfig()->getArguments();
    }

    public function  getArgument(string $argumentName): AbstractType|InputFieldInterface
    {
        return $this->getConfig()->getArgument($argumentName);
    }
    
    public function hasArgument(string $argumentName): bool
    {
        return $this->getConfig()->hasArgument($argumentName);
    }
    
    public function hasArguments(): bool
    {
        return $this->hasArgumentCache ?? ($this->hasArgumentCache = $this->getConfig()->hasArguments());
    }
}
