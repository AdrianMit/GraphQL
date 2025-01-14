<?php

namespace Dreamlabs\GraphQL\Config\Schema;


use Exception;
use Dreamlabs\GraphQL\Config\AbstractConfig;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\Object\ObjectType;
use Dreamlabs\GraphQL\Type\SchemaDirectivesList;
use Dreamlabs\GraphQL\Type\SchemaTypesList;
use Dreamlabs\GraphQL\Type\TypeService;

class SchemaConfig extends AbstractConfig
{
    private SchemaTypesList $typesList;
    private SchemaDirectivesList $directiveList;

    public function __construct(array $configData, ?object $contextObject = null, ?bool $finalClass = false)
    {
        $this->typesList = new SchemaTypesList();
        $this->directiveList = new SchemaDirectivesList();
        parent::__construct($configData, $contextObject, $finalClass);
    }


    public function getRules(): array
    {
        return [
            'query'      => ['type' => TypeService::TYPE_OBJECT_TYPE, 'required' => true],
            'mutation'   => ['type' => TypeService::TYPE_OBJECT_TYPE],
            'types'      => ['type' => TypeService::TYPE_ARRAY],
            'directives' => ['type' => TypeService::TYPE_ARRAY],
            'name'       => ['type' => TypeService::TYPE_STRING],
        ];
    }
    
    /**
     * @throws Exception
     */
    protected function build(): void
    {
        parent::build();
        if (!empty($this->data['types'])) {
            $this->typesList->addTypes($this->data['types']);
        }
        if (!empty($this->data['directives'])) {
            $this->directiveList->addDirectives($this->data['directives']);
        }
    }
    
    public function getQuery(): AbstractObjectType
    {
        return $this->data['query'];
    }

    public function setQuery($query): static
    {
        $this->data['query'] = $query;

        return $this;
    }

    public function getMutation()
    {
        return $this->get('mutation');
    }

    public function setMutation($query): static
    {
        $this->data['mutation'] = $query;

        return $this;
    }

    public function getName(): mixed
    {
        return $this->get('name', 'RootSchema');
    }

    public function getTypesList(): SchemaTypesList
    {
        return $this->typesList;
    }

    public function getDirectiveList(): SchemaDirectivesList
    {
        return $this->directiveList;
    }
}
