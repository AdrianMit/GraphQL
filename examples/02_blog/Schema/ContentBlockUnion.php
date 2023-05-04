<?php
/**
 * ContentBlockUnion.php
 */

namespace Examples\Blog\Schema;

use Dreamlabs\GraphQL\Type\Union\AbstractUnionType;

class ContentBlockUnion extends AbstractUnionType
{
    public function getTypes(): array
    {
        return [new PostType(), new BannerType()];
    }

    public function resolveType($object): BannerType|PostType|null
    {
        return empty($object['id']) ? null : (str_contains($object['id'], 'post') ? new PostType() : new BannerType());
    }

}
