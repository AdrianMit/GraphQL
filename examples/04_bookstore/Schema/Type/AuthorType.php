<?php

namespace Examples\BookStore\Schema\Type;

use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;
use Dreamlabs\GraphQL\Exception\ConfigurationException;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\Scalar\IdType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class AuthorType extends AbstractObjectType
{
    /**
     * @throws ConfigurationException
     */
    public function build(ObjectTypeConfig $config): void
    {
        $config->addFields([
            'id'        => new NonNullType(new IdType()),
            'firstName' => new StringType(),
            'lastName'  => new StringType(),
        ]);
    }
}
