<?php

namespace Examples\BookStore;

class DataProvider
{
    static private array $authors = [
        '1' => [
            'id' => '1',
            'firstName' => 'Mark',
            'lastName' => 'Twain'
        ]
    ];

    public static function getBooks(): array
    {
        return [
            [
                'id' => 1,
                'title' => 'The Adventures of Tom Sawyer',
                'year' => 1876,
                'isbn' => '978-0996584838',
                'author' => self::$authors['1']
            ],
        ];
    }

    public static function getAuthors(): array
    {
        return self::$authors;
    }
}
