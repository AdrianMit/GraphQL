<?php
/**
 * BannerType.php
 */

namespace Examples\Blog\Schema;

use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;
use Dreamlabs\GraphQL\Exception\ConfigurationException;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class BannerType extends AbstractObjectType
{
    /**
     * @throws ConfigurationException
     */
    public function build(ObjectTypeConfig $config): void
    {
        $config
            ->addField('title', new NonNullType(new StringType()))
            ->addField('summary', new StringType())
            ->addField('imageLink', new StringType());
    }

    public function getInterfaces(): array
    {
        return [new ContentBlockInterface()];
    }
}
