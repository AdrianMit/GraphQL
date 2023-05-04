<?php

namespace Dreamlabs\GraphQL\Type\Enum;

use Dreamlabs\GraphQL\Config\Object\EnumTypeConfig;

final class EnumType extends AbstractEnumType
{

    public function __construct(array $config)
    {
        $this->config = new EnumTypeConfig($config, $this, true);
    }

    public function getValues()
    {
        return $this->getConfig()->getValues();
    }

}
