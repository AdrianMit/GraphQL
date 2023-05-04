<?php

namespace Dreamlabs\GraphQL\Relay\Field;


use Dreamlabs\GraphQL\Config\Field\FieldConfig;
use Dreamlabs\GraphQL\Execution\ResolveInfo;
use Dreamlabs\GraphQL\Field\AbstractField;
use Dreamlabs\GraphQL\Field\InputField;
use Dreamlabs\GraphQL\Relay\Fetcher\FetcherInterface;
use Dreamlabs\GraphQL\Relay\Node;
use Dreamlabs\GraphQL\Relay\NodeInterfaceType;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Scalar\IdType;

class NodeField extends AbstractField
{

    protected NodeInterfaceType $type;

    public function __construct(protected FetcherInterface $fetcher)
    {
        $this->type    = (new NodeInterfaceType())->setFetcher($this->fetcher);

        parent::__construct([]);
    }

    public function getName()
    {
        return 'node';
    }

    public function getDescription(): string
    {
        return 'Fetches an object given its ID';
    }

    public function build(FieldConfig $config): void
    {
        $config->addArgument(new InputField([
            'name'        => 'id',
            'type'        => new NonNullType(new IdType()),
            'description' => 'The ID of an object'
        ]));
    }

    public function getType(): NodeInterfaceType
    {
        return $this->type;
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        [$type, $id] = Node::fromGlobalId($args['id']);

        return $this->fetcher->resolveNode($type, $id);
    }


}
