<?php

namespace Dreamlabs\GraphQL\Execution;


/**
 * Wrapper class for deferred resolvers during execution process.
 * Not part of the public API.
 *
 * @internal
 */
class DeferredResult implements DeferredResolverInterface {

    protected $callback;

    public mixed $result;

    public function __construct(private DeferredResolverInterface $resolver, callable $callback)
    {
        $this->callback = $callback;
    }

    public function resolve(): mixed
    {
        $this->result = call_user_func($this->callback, $this->resolver->resolve());
        
        return;
    }
}
