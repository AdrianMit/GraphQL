<?php

namespace Dreamlabs\GraphQL\Parser\Ast;


trait AstArgumentsTrait
{

    /** @var Argument[] */
    protected $arguments;

    private $argumentsCache = null;


    public function hasArguments()
    {
        return (bool)count($this->arguments);
    }

    public function hasArgument($name): bool
    {
        return array_key_exists($name, $this->arguments);
    }

    /**
     * @return Argument[]
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param $name
     */
    public function getArgument($name): ?Argument
    {
        $argument = null;
        if (isset($this->arguments[$name])) {
            $argument = $this->arguments[$name];
        }

        return $argument;
    }

    public function getArgumentValue($name)
    {
        $argument = $this->getArgument($name);

        return $argument ? $argument->getValue()->getValue() : null;
    }

    /**
     * @param $arguments Argument[]
     */
    public function setArguments(array $arguments): void
    {
        $this->arguments = [];
        $this->argumentsCache = null;

        foreach ($arguments as $argument) {
            $this->addArgument($argument);
        }
    }

    public function addArgument(Argument $argument): void
    {
        $this->arguments[$argument->getName()] = $argument;
    }

    public function getKeyValueArguments()
    {
        if ($this->argumentsCache !== null) {
            return $this->argumentsCache;
        }

        $this->argumentsCache = [];

        foreach ($this->getArguments() as $argument) {
            $this->argumentsCache[$argument->getName()] = $argument->getValue()->getValue();
        }

        return $this->argumentsCache;
    }
}
