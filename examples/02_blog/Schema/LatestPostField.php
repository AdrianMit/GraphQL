<?php

namespace Examples\Blog\Schema;


use BlogTest\PostType;
use Dreamlabs\GraphQL\Execution\ResolveInfo;
use Dreamlabs\GraphQL\Field\AbstractField;

class LatestPostField extends AbstractField
{
    public function getType(): PostType
    {
        return new PostType();
    }

    public function resolve($value, array $args, ResolveInfo $info): array
    {
        return [
            "title"   => "New approach in API has been revealed",
            "summary" => "This post describes a new approach to create and maintain APIs",
        ];
    }


}
