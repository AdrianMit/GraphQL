<?php

namespace Examples\BookStore\Schema\Field\Book;


use Examples\BookStore\DataProvider;
use Examples\BookStore\Schema\Type\BookType;
use Dreamlabs\GraphQL\Execution\ResolveInfo;
use Dreamlabs\GraphQL\Field\AbstractField;
use Dreamlabs\GraphQL\Type\ListType\ListType;

class RecentBooksField extends AbstractField
{
    public function getType(): ListType
    {
        return new ListType(new BookType());
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        return DataProvider::getBooks();
    }
}
