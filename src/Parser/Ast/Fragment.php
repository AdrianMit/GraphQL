<?php

namespace Dreamlabs\GraphQL\Parser\Ast;


use Dreamlabs\GraphQL\Parser\Location;

class Fragment extends AbstractAst
{

    use AstDirectivesTrait;

    private bool $used = false;

    /**
     * @param string          $name
     * @param string          $model
     * @param array           $directives
     * @param Field[]|Query[] $fields
     * @param Location        $location
     */
    public function __construct(protected $name, protected $model, array $directives, protected array $fields, Location $location)
    {
        parent::__construct($location);
        $this->setDirectives($directives);
    }

    public function isUsed(): bool
    {
        return $this->used;
    }

    /**
     * @param boolean $used
     */
    public function setUsed($used): void
    {
        $this->used = $used;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    public function setName(mixed $name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    public function setModel(mixed $model): void
    {
        $this->model = $model;
    }

    /**
     * @return Field[]|Query[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param Field[]|Query[] $fields
     */
    public function setFields($fields): void
    {
        $this->fields = $fields;
    }
}
