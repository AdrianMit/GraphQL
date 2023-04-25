<?php
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 5:07 PM 5/14/16
 */

namespace Youshido\GraphQL\Type\Traits;


use Youshido\GraphQL\Config\Traits\ConfigAwareTrait;
use Youshido\GraphQL\Field\InputField;

/**
 * Class ArgumentsAwareObjectTrait
 * @package    Youshido\GraphQL\Type\Traits
 * @codeCoverageIgnore
 *
 * @deprecated To be removed during the release optimization
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
