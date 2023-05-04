<?php

namespace Dreamlabs\GraphQL\Type\ListType;


use Dreamlabs\GraphQL\Config\Object\ListTypeConfig;

final class ListType extends AbstractListType
{

    public function __construct($itemType)
    {
        $this->config = new ListTypeConfig(['itemType' => $itemType], $this, true);
    }

    public function getItemType()
    {
        return $this->getConfig()->get('itemType');
    }

    public function getName(): ?string
    {
        return null;
    }
}
