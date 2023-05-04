<?php

namespace Dreamlabs\GraphQL\Type\Object;

use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;

final class ObjectType extends AbstractObjectType
{

    public function __construct(array $config)
    {
        $this->config = new ObjectTypeConfig($config, $this, true);
    }

    /**
     * @inheritdoc
     * 
     * @codeCoverageIgnore
     */
    public function build(ObjectTypeConfig $config): void { }

    public function getName(): string
    {
        return $this->getConfigValue('name');
    }
}
