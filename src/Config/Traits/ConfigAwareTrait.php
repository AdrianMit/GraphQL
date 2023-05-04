<?php

namespace Dreamlabs\GraphQL\Config\Traits;

use Dreamlabs\GraphQL\Config\AbstractConfig;
use Dreamlabs\GraphQL\Config\Field\FieldConfig;
use Dreamlabs\GraphQL\Config\Field\InputFieldConfig;
use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;

trait ConfigAwareTrait
{
    protected AbstractConfig $config;
    protected array $configCache = [];

    public function getConfig(): AbstractConfig
    {
        return $this->config;
    }

    protected function getConfigValue(string $key, mixed $defaultValue = null)
    {
        if (array_key_exists($key, $this->configCache)) {
            return $this->configCache[$key];
        }
        $this->configCache[$key] = !empty($this->config) ? $this->config->get($key, $defaultValue) : $defaultValue;
        return $this->configCache[$key];
    }

    public function getDescription(): string
    {
        return $this->getConfigValue('description');
    }

}
