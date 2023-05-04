<?php

namespace Examples\BookStore\Schema\Type;


use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;
use Dreamlabs\GraphQL\Exception\ConfigurationException;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\Scalar\IdType;
use Dreamlabs\GraphQL\Type\Scalar\IntType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class BookType extends AbstractObjectType
{
    /**
     * @throws ConfigurationException
     */
    public function build(ObjectTypeConfig $config): void
    {
        $config->addFields([
            'id'     => new IdType(),
            'title'  => new StringType(),
            'year'   => new IntType(),
            'isbn'   => new StringType(),
            'author' => new AuthorType(),
        ]);
    }

}
