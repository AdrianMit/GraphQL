<?php

namespace Examples\BookStore\Schema\Type;

use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;
use Dreamlabs\GraphQL\Exception\ConfigurationException;
use Examples\BookStore\Schema\Field\CategoriesField;
use Dreamlabs\GraphQL\Type\ListType\ListType;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\Scalar\IdType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class CategoryType extends AbstractObjectType
{
    /**
     * @throws ConfigurationException
     */
    public function build(ObjectTypeConfig $config): void
    {
        $config->addFields([
            'id'      => new IdType(),
            'title'   => new StringType(),
            'authors' => new ListType(new AuthorType()),
            new CategoriesField(),
        ]);
    }
}
