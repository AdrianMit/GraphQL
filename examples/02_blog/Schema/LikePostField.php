<?php
/**
 * LikePost.php
 */

namespace Examples\Blog\Schema;

use Dreamlabs\GraphQL\Config\Field\FieldConfig;
use Dreamlabs\GraphQL\Exception\ConfigurationException;
use Dreamlabs\GraphQL\Execution\ResolveInfo;
use Dreamlabs\GraphQL\Field\AbstractField;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Scalar\IntType;

class LikePostField extends AbstractField
{
    public function resolve($value, array $args, ResolveInfo $info): mixed
    {
        return $info->getReturnType()->getOne($args['id']);
    }

    public function getType(): PostType
    {
        return new PostType();
    }
    
    /**
     * @throws ConfigurationException
     */
    public function build(FieldConfig $config): void
    {
        $config->addArgument('id', new NonNullType(new IntType()));
    }


}
