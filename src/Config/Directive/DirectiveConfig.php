<?php

namespace Dreamlabs\GraphQL\Config\Directive;


use Dreamlabs\GraphQL\Config\AbstractConfig;
use Dreamlabs\GraphQL\Config\Traits\ArgumentsAwareConfigTrait;
use Dreamlabs\GraphQL\Type\TypeService;

/**
 * Class DirectiveConfig
 *
 * @package Dreamlabs\GraphQL\Config\Directive
 */
class DirectiveConfig extends AbstractConfig
{
    use ArgumentsAwareConfigTrait;

    protected array $locations = [];

    public function getRules(): array
    {
        return [
            'name'        => ['type' => TypeService::TYPE_STRING, 'final' => true],
            'description' => ['type' => TypeService::TYPE_STRING],
            'args'        => ['type' => TypeService::TYPE_ARRAY],
            'locations'   => ['type' => TypeService::TYPE_ARRAY],
        ];
    }

    public function getLocations(): array
    {
        return $this->locations;
    }

    public function build(): void
    {
        $this->buildArguments();

        if (!empty($this->data['locations'])) {
            foreach ($this->data['locations'] as $location) {
                $this->locations[] = $location;
            }
        }
    }

}
