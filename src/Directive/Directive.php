<?php

namespace Dreamlabs\GraphQL\Directive;

use Dreamlabs\GraphQL\Config\AbstractConfig;
use Dreamlabs\GraphQL\Config\Directive\DirectiveConfig;
use Dreamlabs\GraphQL\Config\Field\FieldConfig;
use Dreamlabs\GraphQL\Config\Field\InputFieldConfig;
use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;
use Dreamlabs\GraphQL\Exception\ConfigurationException;
use Dreamlabs\GraphQL\Type\Traits\ArgumentsAwareObjectTrait;
use Dreamlabs\GraphQL\Type\Traits\AutoNameTrait;

class Directive implements DirectiveInterface
{
    use ArgumentsAwareObjectTrait;
    use AutoNameTrait;

    protected bool $isFinal = false;
    
    /**
     * @throws ConfigurationException
     */
    public function __construct(array $config = [])
    {
        if (empty($config['name'])) {
            $config['name'] = $this->getName();
        }

        $this->config = new DirectiveConfig($config, $this, $this->isFinal);
        $this->build($this->config);
    }

    public function build(DirectiveConfig $config): void
    {
    }

    public function addArguments(array $argumentsList): AbstractConfig
    {
        return $this->getConfig()->addArguments($argumentsList);
    }

}
