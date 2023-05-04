<?php

namespace Dreamlabs\GraphQL\Execution;

use Dreamlabs\GraphQL\Exception\Parser\InvalidRequestException;
use Dreamlabs\GraphQL\Parser\Ast\ArgumentValue\Variable;
use Dreamlabs\GraphQL\Parser\Ast\ArgumentValue\VariableReference;
use Dreamlabs\GraphQL\Parser\Ast\Fragment;
use Dreamlabs\GraphQL\Parser\Ast\FragmentReference;
use Dreamlabs\GraphQL\Parser\Ast\Mutation;
use Dreamlabs\GraphQL\Parser\Ast\Query;
use JsonException;

class Request
{

    /** @var  Query[] */
    private array $queries = [];

    /** @var Fragment[] */
    private array $fragments = [];

    /** @var Mutation[] */
    private array $mutations = [];

    private array $variables = [];

    /** @var VariableReference[] */
    private array $variableReferences = [];

    private array $queryVariables = [];

    private array $fragmentReferences = [];

    public function __construct($data = [], $variables = [])
    {
        if (array_key_exists('queries', $data)) {
            $this->addQueries($data['queries']);
        }

        if (array_key_exists('mutations', $data)) {
            $this->addMutations($data['mutations']);
        }

        if (array_key_exists('fragments', $data)) {
            $this->addFragments($data['fragments']);
        }

        if (array_key_exists('fragmentReferences', $data)) {
            $this->addFragmentReferences($data['fragmentReferences']);
        }

        if (array_key_exists('variables', $data)) {
            $this->addQueryVariables($data['variables']);
        }

        if (array_key_exists('variableReferences', $data)) {
            foreach ($data['variableReferences'] as $ref) {
                if (!array_key_exists($ref->getName(), $variables)) {
                    /** @var Variable $variable */
                    $variable = $ref->getVariable();
                    if ($variable->hasDefaultValue()) {
                        $variables[$variable->getName()] = $variable->getDefaultValue()->getValue();
                        continue;
                    }
                    throw new InvalidRequestException(sprintf("Variable %s hasn't been submitted", $ref->getName()), $ref->getLocation());
                }
            }

            $this->addVariableReferences($data['variableReferences']);
        }

        $this->setVariables($variables);
    }

    public function addQueries($queries): void
    {
        foreach ($queries as $query) {
            $this->queries[] = $query;
        }
    }

    public function addMutations($mutations): void
    {
        foreach ($mutations as $mutation) {
            $this->mutations[] = $mutation;
        }
    }

    public function addQueryVariables($queryVariables): void
    {
        foreach ($queryVariables as $queryVariable) {
            $this->queryVariables[] = $queryVariable;
        }
    }

    public function addVariableReferences($variableReferences): void
    {
        foreach ($variableReferences as $variableReference) {
            $this->variableReferences[] = $variableReference;
        }
    }

    public function addFragmentReferences($fragmentReferences): void
    {
        foreach ($fragmentReferences as $fragmentReference) {
            $this->fragmentReferences[] = $fragmentReference;
        }
    }

    public function addFragments($fragments): void
    {
        foreach ($fragments as $fragment) {
            $this->addFragment($fragment);
        }
    }

    /**
     * @return Query[]
     */
    public function getAllOperations(): array
    {
        return array_merge($this->mutations, $this->queries);
    }

    /**
     * @return Query[]
     */
    public function getQueries(): array
    {
        return $this->queries;
    }

    /**
     * @return Fragment[]
     */
    public function getFragments(): array
    {
        return $this->fragments;
    }

    public function addFragment(Fragment $fragment): void
    {
        $this->fragments[] = $fragment;
    }

    /**
     * @param $name
     */
    public function getFragment($name): ?Fragment
    {
        foreach ($this->fragments as $fragment) {
            if ($fragment->getName() == $name) {
                return $fragment;
            }
        }

        return null;
    }

    /**
     * @return Mutation[]
     */
    public function getMutations(): array
    {
        return $this->mutations;
    }

    /**
     * @return bool
     */
    public function hasQueries()
    {
        return (bool)count($this->queries);
    }

    /**
     * @return bool
     */
    public function hasMutations()
    {
        return (bool)count($this->mutations);
    }

    /**
     * @return bool
     */
    public function hasFragments()
    {
        return (bool)count($this->fragments);
    }

    public function getVariables(): array
    {
        return $this->variables;
    }
    
    /**
     * @throws JsonException
     */
    public function setVariables(array|string $variables): static
    {
        if (!is_array($variables)) {
            $variables = json_decode($variables, true, 512, JSON_THROW_ON_ERROR);
        }

        $this->variables = $variables;
        foreach ($this->variableReferences as $reference) {
            /** invalid request with no variable */
            if (!$reference->getVariable()) continue;
            $variableName = $reference->getVariable()->getName();

            /** no variable was set at the time */
            if (!array_key_exists($variableName, $variables)) continue;

            $reference->getVariable()->setValue($variables[$variableName]);
            $reference->setValue($variables[$variableName]);
        }

        return $this;
    }

    public function getVariable($name)
    {
        return $this->hasVariable($name) ? $this->variables[$name] : null;
    }

    public function hasVariable($name): bool
    {
        return array_key_exists($name, $this->variables);
    }

    /**
     * @return array|Variable[]
     */
    public function getQueryVariables(): array
    {
        return $this->queryVariables;
    }

    public function setQueryVariables(array $queryVariables): void
    {
        $this->queryVariables = $queryVariables;
    }

    public function getFragmentReferences(): array
    {
        return $this->fragmentReferences;
    }
    
    public function setFragmentReferences(array $fragmentReferences): void
    {
        $this->fragmentReferences = $fragmentReferences;
    }

    /**
     * @return array|VariableReference[]
     */
    public function getVariableReferences(): array
    {
        return $this->variableReferences;
    }
}
