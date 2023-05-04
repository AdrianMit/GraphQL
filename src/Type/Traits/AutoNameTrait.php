<?php

namespace Dreamlabs\GraphQL\Type\Traits;
use Dreamlabs\GraphQL\Field\FieldInterface;

/**
 * Class AutoNameTrait
 * @package Dreamlabs\GraphQL\Type\Traits
 */
trait AutoNameTrait
{
    public function getName(): ?string
    {
        if (!empty($this->config)) {
            return $this->config->getName();
        }

        $className = static::class;

        if ($prevPos = strrpos($className, '\\')) {
            $className = substr($className, $prevPos + 1);
        }
        if (str_ends_with($className, 'Field')) {
            $className = lcfirst(substr($className, 0, -5));
        } elseif (str_ends_with($className, 'Type')) {
            $className = substr($className, 0, -4);
        }

        if ($this instanceof FieldInterface) {
            $className = lcfirst($className);
        }


        return $className;
    }

}
