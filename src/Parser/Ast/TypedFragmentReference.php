<?php

namespace Dreamlabs\GraphQL\Parser\Ast;


use Dreamlabs\GraphQL\Parser\Ast\Interfaces\FragmentInterface;
use Dreamlabs\GraphQL\Parser\Location;

class TypedFragmentReference extends AbstractAst implements FragmentInterface
{
    use AstDirectivesTrait;

    /**
     * @param string          $typeName
     * @param Field[]|Query[] $fields
     * @param Directive[]     $directives
     * @param Location        $location
     */
    public function __construct(protected $typeName, protected array $fields, array $directives, Location $location)
    {
        parent::__construct($location);
        $this->setDirectives($directives);
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

    /**
     * @return string
     */
    public function getTypeName()
    {
        return $this->typeName;
    }

    /**
     * @param string $typeName
     */
    public function setTypeName($typeName): void
    {
        $this->typeName = $typeName;
    }

}
