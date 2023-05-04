<?php

namespace Dreamlabs\GraphQL\Field;


use Dreamlabs\GraphQL\Execution\ResolveInfo;

interface FieldInterface extends InputFieldInterface
{
    public function resolve($value, array $args, ResolveInfo $info);

    public function getResolveFunction();
}
