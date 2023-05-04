<?php

namespace Dreamlabs\GraphQL\Relay\Field;


use Dreamlabs\GraphQL\Execution\ResolveInfo;
use Dreamlabs\GraphQL\Field\AbstractField;
use Dreamlabs\GraphQL\Relay\Node;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Scalar\IdType;

class GlobalIdField extends AbstractField
{

    /**
     * @param string $typeName
     */
    public function __construct(protected $typeName)
    {
        $config = [
            'type'    => $this->getType(),
            'name'    => $this->getName(),
            'resolve' => [$this, 'resolve']
        ];

        parent::__construct($config);
    }

    public function getName()
    {
        return 'id';
    }

    public function getDescription(): string
    {
        return 'The ID of an object';
    }

    public function getType(): NonNullType
    {
        return new NonNullType(new IdType());
    }

    /**
     * @inheritdoc
     */
    public function resolve($value, array $args, ResolveInfo $info)
    {
        return $value ? Node::toGlobalId($this->typeName, $value['id']) : null;
    }
}
