<?php
/**
 * This file is a part of GraphQL project.
 *
 * @author Philipp Melab <philipp.melab@amazee.com>
 * created: 7/25/17 12:34 PM
 */

namespace Youshido\GraphQL\Execution;


/**
 * Wrapper class for deferred resolvers during execution process.
 * Not part of the public API.
 *
 * @internal
 */
class DeferredResult implements DeferredResolverInterface {

    /** @var $callback callable */
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
