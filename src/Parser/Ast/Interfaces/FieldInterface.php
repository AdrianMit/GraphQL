<?php
namespace Dreamlabs\GraphQL\Parser\Ast\Interfaces;


use Dreamlabs\GraphQL\Parser\Ast\Argument;

interface FieldInterface extends LocatableInterface
{

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getAlias();

    /**
     * @return Argument[]
     */
    public function getArguments();

    /**
     * @param string $name
     *
     * @return Argument
     */
    public function getArgument($name);

    /**
     * @return bool
     */
    public function hasFields();

    /**
     * @return array
     */
    public function getFields();

}
