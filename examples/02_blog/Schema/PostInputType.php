<?php
/**
 * PostInputType.php
 */

namespace Examples\Blog\Schema;


use Dreamlabs\GraphQL\Exception\ConfigurationException;
use Dreamlabs\GraphQL\Type\InputObject\AbstractInputObjectType;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class PostInputType extends AbstractInputObjectType
{
    
    /**
     * @throws ConfigurationException
     */
    public function build($config): void
    {
        $config
            ->addField('title', new NonNullType(new StringType()))
            ->addField('summary', new StringType());
    }


}
