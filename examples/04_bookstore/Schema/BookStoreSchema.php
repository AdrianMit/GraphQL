<?php

namespace Examples\BookStore\Schema;


use Examples\BookStore\DataProvider;
use Examples\BookStore\Schema\Field\Book\RecentBooksField;
use Examples\BookStore\Schema\Field\CategoriesField;
use Examples\BookStore\Schema\Type\AuthorType;
use Dreamlabs\GraphQL\Config\Schema\SchemaConfig;
use Dreamlabs\GraphQL\Schema\AbstractSchema;
use Dreamlabs\GraphQL\Type\ListType\ListType;

class BookStoreSchema extends AbstractSchema
{
    public function build(SchemaConfig $config): void
    {
        $config->getQuery()->addFields([
            'authors' => [
                'type'    => new ListType(new AuthorType()),
                'resolve' => function () {
                    return DataProvider::getAuthors();
                }
            ],
            new RecentBooksField(),
            new CategoriesField(),
        ]);
    }
}
