<?php

namespace Dreamlabs\GraphQL\Field;


use Dreamlabs\GraphQL\Type\AbstractType;

final class InputField extends AbstractInputField
{

    protected bool $isFinal = false;

    public function getType(): AbstractType
    {
        return $this->getConfigValue('type');
    }
}
