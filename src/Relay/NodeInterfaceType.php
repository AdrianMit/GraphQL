<?php

namespace Dreamlabs\GraphQL\Relay;


use Dreamlabs\GraphQL\Relay\Fetcher\FetcherInterface;
use Dreamlabs\GraphQL\Relay\Field\GlobalIdField;
use Dreamlabs\GraphQL\Type\InterfaceType\AbstractInterfaceType;

class NodeInterfaceType extends AbstractInterfaceType
{

    /** @var  FetcherInterface */ //todo: maybe there are better solution
    protected $fetcher;

    public function getName(): string
    {
        return 'NodeInterface';
    }

    public function build($config): void
    {
        $config->addField(new GlobalIdField('NodeInterface'));
    }

    public function resolveType($object)
    {
        if ($this->fetcher) {
            return $this->fetcher->resolveType($object);
        }

        return null;
    }

    /**
     * @return FetcherInterface
     */
    public function getFetcher()
    {
        return $this->fetcher;
    }

    /**
     * @param FetcherInterface $fetcher
     *
     * @return NodeInterfaceType
     */
    public function setFetcher($fetcher)
    {
        $this->fetcher = $fetcher;

        return $this;
    }
}
