<?php

namespace Dreamlabs\GraphQL\Type\InputObject;

use Dreamlabs\GraphQL\Config\Object\InputObjectTypeConfig;

final class InputObjectType extends AbstractInputObjectType
{

    public function __construct($config)
    {
        $this->config = new InputObjectTypeConfig($config, $this, true);
    }

    /**
     * @inheritdoc
     * 
     * @codeCoverageIgnore
     */
    public function build($config): void
    {
    }
}
