<?php

namespace Examples\BookStore\Schema\Field;


use Examples\BookStore\Schema\Type\CategoryType;
use Dreamlabs\GraphQL\Execution\DeferredResolver;
use Dreamlabs\GraphQL\Execution\ResolveInfo;
use Dreamlabs\GraphQL\Field\AbstractField;
use Dreamlabs\GraphQL\Type\ListType\ListType;

class CategoriesField extends AbstractField
{
    public function getType(): ListType
    {
        return new ListType(new CategoryType());
    }

    public function resolve($value, array $args, ResolveInfo $info): DeferredResolver
    {
        return new DeferredResolver(function () use ($value) {
            $id = empty($value['id']) ? "1" : $value['id'] . ".1";

            return [
                'id'       => $id,
                'title'    => 'Category ' . $id,
                'authors'  => [
                    [
                        'id'        => 'author1',
                        'firstName' => 'John',
                        'lastName'  => 'Doe',
                    ],
                ],
                'children' => [],
            ];
        });
    }
}
