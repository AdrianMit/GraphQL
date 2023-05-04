<?php

namespace Dreamlabs\GraphQL\Execution;


/**
 * Default implementation of DeferredResolverInterface
 *
 * @package Dreamlabs\GraphQL\Execution
 */
class DeferredResolver implements DeferredResolverInterface {

    public function __construct(
        private $resolver
    )
    {
    }

    public function resolve(): mixed
    {
      return call_user_func($this->resolver);
    }
}
