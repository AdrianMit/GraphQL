<?php

namespace Dreamlabs\Tests\DataProvider;


use Dreamlabs\GraphQL\Type\ListType\AbstractListType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class TestListType extends AbstractListType
{
    public function getItemType(): StringType
    {
        return new StringType();
    }


}
