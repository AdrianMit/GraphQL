<?php

namespace Dreamlabs\Tests\DataProvider;


use Dreamlabs\GraphQL\Field\AbstractInputField;
use Dreamlabs\GraphQL\Type\InputTypeInterface;
use Dreamlabs\GraphQL\Type\Scalar\IntType;

class TestInputField extends AbstractInputField
{

    /**
     * @return InputTypeInterface
     */
    public function getType(): IntType
    {
        return new IntType();
    }

    public function getDescription(): string
    {
        return 'description';
    }

    public function getDefaultValue()
    {
        return 'default';
    }
}
