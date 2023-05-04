<?php

namespace Dreamlabs\GraphQL\Type\Object;

abstract class AbstractMutationObjectType extends AbstractObjectType
{

    public function getType()
    {
        return $this->getOutputType();
    }

    abstract public function getOutputType();
}
