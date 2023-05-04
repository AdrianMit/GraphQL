<?php
/**
 * PostStatus.php
 */

namespace Examples\Blog\Schema;


use Dreamlabs\GraphQL\Type\Enum\AbstractEnumType;

class PostStatus extends AbstractEnumType
{
    public function getValues(): array
    {
        return [
            [
                'value' => 0,
                'name'  => 'DRAFT',
            ],
            [
                'value' => 1,
                'name'  => 'PUBLISHED',
            ]
        ];
    }

}
