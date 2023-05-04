<?php
namespace Dreamlabs\GraphQL\Type\InterfaceType;


use Dreamlabs\GraphQL\Config\Object\InterfaceTypeConfig;

final class InterfaceType extends AbstractInterfaceType
{

    public function __construct($config = [])
    {
        $this->config = new InterfaceTypeConfig($config, $this, true);
    }

    /**
     * @inheritdoc
     * 
     * @codeCoverageIgnore
     */
    public function build($config): void
    {
    }

    public function resolveType($object)
    {
        return $this->getConfig()->resolveType($object);
    }

}
