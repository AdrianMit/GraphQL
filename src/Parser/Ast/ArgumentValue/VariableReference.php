<?php
/**
 * Date: 10/24/16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Parser\Ast\ArgumentValue;


use Youshido\GraphQL\Parser\Ast\AbstractAst;
use Youshido\GraphQL\Parser\Ast\Interfaces\ValueInterface;
use Youshido\GraphQL\Parser\Location;

class VariableReference extends AbstractAst implements ValueInterface
{

    /** @var  mixed */
    private $value;

    /**
     * @param string        $name
     * @param Variable|null $variable
     * @param Location      $location
     */
    public function __construct(private $name, Location $location, private ?Variable $variable = null)
    {
        parent::__construct($location);
    }

    public function getVariable()
    {
        return $this->variable;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
