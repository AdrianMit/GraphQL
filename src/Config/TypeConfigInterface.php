<?php

namespace Dreamlabs\GraphQL\Config;


use Dreamlabs\GraphQL\Field\AbstractField;
use Dreamlabs\GraphQL\Field\Field;
use Dreamlabs\GraphQL\Field\FieldInterface;
use Dreamlabs\GraphQL\Type\AbstractType;

interface TypeConfigInterface
{
    public function addField(Field|string $field, AbstractType|array|null $fieldInfo = null);

    public function getField(string $name): ?FieldInterface;

    public function removeField(string$name);

    public function hasField(string$name);

    public function getFields();
}
