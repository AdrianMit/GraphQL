<?php

namespace Dreamlabs\GraphQL\Relay\Type;


use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\Scalar\BooleanType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class PageInfoType extends AbstractObjectType
{
    public function build(ObjectTypeConfig $config): void
    {
        $config->addFields([
            'hasNextPage'     => [
                'type'        => new NonNullType(new BooleanType()),
                'description' => 'When paginating forwards, are there more items?'
            ],
            'hasPreviousPage' => [
                'type'        => new NonNullType(new BooleanType()),
                'description' => 'When paginating backwards, are there more items?'
            ],
            'startCursor'     => [
                'type'        => new StringType(),
                'description' => 'When paginating backwards, the cursor to continue.'
            ],
            'endCursor'       => [
                'type'        => new StringType(),
                'description' => 'When paginating forwards, the cursor to continue.'
            ],
        ]);
    }

    public function getDescription(): string
    {
        return "Information about pagination in a connection.";
    }

}
