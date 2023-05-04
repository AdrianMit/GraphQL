<?php

namespace Dreamlabs\GraphQL\Type\Traits;

use Dreamlabs\GraphQL\Config\Traits\ConfigAwareTrait;
use Dreamlabs\GraphQL\Field\InputField;

/**
 * Class ArgumentsAwareObjectTrait
 * @package    Dreamlabs\GraphQL\Type\Traits
 * @codeCoverageIgnore
 */
trait ArgumentsAwareObjectTrait
{
    use ConfigAwareTrait;

    public function addArgument($argument, $argumentInfo = null)
    {
        return $this->getConfig()->addArgument($argument, $argumentInfo);
    }

    public function removeArgument($argumentName)
    {
        return $this->getConfig()->removeArgument($argumentName);
    }

    public function getArguments(): array
    {
        return $this->getConfig()->getArguments();
    }

    public function getArgument(string $argumentName): ?InputField
    {
        return $this->getConfig()->getArgument($argumentName);
    }

    public function hasArgument(string $argumentName): bool
    {
        return $this->getConfig()->hasArgument($argumentName);
    }

    public function hasArguments(): bool
    {
        return $this->getConfig()->hasArguments();
    }

}
