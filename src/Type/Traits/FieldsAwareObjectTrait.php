<?php

namespace Dreamlabs\GraphQL\Type\Traits;


use Dreamlabs\GraphQL\Config\Traits\ConfigAwareTrait;
use Dreamlabs\GraphQL\Field\FieldInterface;

trait FieldsAwareObjectTrait
{
    use ConfigAwareTrait;

    public function addFields($fieldsList)
    {
        $this->getConfig()->addFields($fieldsList);

        return $this;
    }

    public function addField($field, $fieldInfo = null)
    {
        $this->getConfig()->addField($field, $fieldInfo);

        return $this;
    }

    public function getFields()
    {
        return $this->getConfig()->getFields();
    }

    public function getField($fieldName): ?FieldInterface
    {
        return $this->getConfig()->getField($fieldName);
    }

    public function hasField($fieldName)
    {
        return $this->getConfig()->hasField($fieldName);
    }

    public function hasFields()
    {
        return $this->getConfig()->hasFields();
    }

}
