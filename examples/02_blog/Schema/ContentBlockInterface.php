<?php
/**
 * ContentBlockInterface.php
 */

namespace Examples\Blog\Schema;


use Dreamlabs\GraphQL\Exception\ConfigurationException;
use Dreamlabs\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class ContentBlockInterface extends AbstractInterfaceType
{
    
    /**
     * @throws ConfigurationException
     */
    public function build($config): void
    {
        $config->addField('title', new NonNullType(new StringType()));
        $config->addField('summary', new StringType());
    }

    public function resolveType($object): BannerType|PostType|null
    {
        return empty($object['id']) ? null : (str_contains($object['id'], 'post') ? new PostType() : new BannerType());
    }
}
