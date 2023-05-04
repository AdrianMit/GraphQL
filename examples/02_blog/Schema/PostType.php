<?php
/**
 * PostType.php
 */

namespace Examples\Blog\Schema;

use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;
use Dreamlabs\GraphQL\Exception\ConfigurationException;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\Scalar\BooleanType;
use Dreamlabs\GraphQL\Type\Scalar\IntType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class PostType extends AbstractObjectType
{
    /**
     * @param ObjectTypeConfig $config
     *
     * @throws ConfigurationException
     */
    public function build(ObjectTypeConfig $config): void
    {
        $config
            ->addField('oldTitle', [
                'type'              => new NonNullType(new StringType()),
                'description'       => 'This field contains a post title',
                'isDeprecated'      => true,
                'deprecationReason' => 'field title is now deprecated',
                'args'              => [
                    'truncated' => new BooleanType()
                ],
                'resolve'           => function ($value, $args) {
                    return (!empty($args['truncated'])) ? explode(' ', $value)[0] . '...' : $value;
                }
            ])
            ->addField(
                'title', [
                    'type'  => new NonNullType(new StringType()),
                    'args'  => [
                        'truncated' => new BooleanType()
                    ],
                ])
            ->addField('status', new PostStatus())
            ->addField('summary', new StringType())
            ->addField('likeCount', new IntType());
    }

    public function getOne($id): array
    {
        return DataProvider::getPost($id);
    }

    public function getInterfaces(): array
    {
        return [new ContentBlockInterface()];
    }
}
