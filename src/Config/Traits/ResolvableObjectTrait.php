<?php


namespace Dreamlabs\GraphQL\Config\Traits;

use Exception;
use Dreamlabs\GraphQL\Execution\ResolveInfo;
use Dreamlabs\GraphQL\Type\TypeMap;
use Dreamlabs\GraphQL\Type\TypeService;

trait ResolvableObjectTrait
{
    /**
     * @throws Exception
     */
    public function resolve(mixed $value, array $args, ResolveInfo $info)
    {
        if ($resolveFunction = $this->getConfig()->getResolveFunction()) {
            return $resolveFunction($value, $args, $info);
        } else {
            if (is_array($value) && array_key_exists($this->getName(), $value)) {
                return $value[$this->getName()];
            } elseif (is_object($value)) {
                return TypeService::getPropertyValue($value, $this->getName());
            } elseif ($this->getType()->getKind() !== TypeMap::KIND_NON_NULL) {
                return null;
            } else {
                throw new Exception(sprintf('Property "%s" not found in resolve result', $this->getName()));
            }
        }
    }


    public function getResolveFunction(): ?callable
    {
        return $this->getConfig()->getResolveFunction();
    }
}
