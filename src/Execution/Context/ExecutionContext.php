<?php

namespace Dreamlabs\GraphQL\Execution\Context;


use Dreamlabs\GraphQL\Field\AbstractField;
use Dreamlabs\GraphQL\Field\FieldInterface;
use Exception;
use Dreamlabs\GraphQL\Execution\Container\ContainerInterface;
use Dreamlabs\GraphQL\Execution\Request;
use Dreamlabs\GraphQL\Field\Field;
use Dreamlabs\GraphQL\Introspection\Field\SchemaField;
use Dreamlabs\GraphQL\Introspection\Field\TypeDefinitionField;
use Dreamlabs\GraphQL\Schema\AbstractSchema;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Validator\ErrorContainer\ErrorContainerTrait;
use Dreamlabs\GraphQL\Validator\SchemaValidator\SchemaValidator;

class ExecutionContext implements ExecutionContextInterface
{

    use ErrorContainerTrait;

    private ?Request $request = null;

    private ?ContainerInterface $container = null;

    private array $typeFieldLookupTable;

    /**
     * ExecutionContext constructor.
     */
    public function __construct(private AbstractSchema $schema)
    {
        $this->validateSchema();

        $this->introduceIntrospectionFields();

        $this->typeFieldLookupTable = [];
    }

    public function getField(AbstractObjectType $type, string $fieldName): ?FieldInterface
    {
        $typeName = $type->getName();

        if (!array_key_exists($typeName, $this->typeFieldLookupTable)) {
            $this->typeFieldLookupTable[$typeName] = [];
        }

        if (!array_key_exists($fieldName, $this->typeFieldLookupTable[$typeName])) {
            $this->typeFieldLookupTable[$typeName][$fieldName] = $type->getField($fieldName);
        }

        return $this->typeFieldLookupTable[$typeName][$fieldName];
    }

    protected function validateSchema(): void
    {
        try {
            (new SchemaValidator())->validate($this->schema);
        } catch (Exception $e) {
            $this->addError($e);
        };
    }

    protected function introduceIntrospectionFields(): void
    {
        $schemaField = new SchemaField();
        $this->schema->addQueryField($schemaField);
        $this->schema->addQueryField(new TypeDefinitionField());
    }

    public function getSchema(): AbstractSchema
    {
        return $this->schema;
    }

    public function setSchema(AbstractSchema $schema): static
    {
        $this->schema = $schema;

        return $this;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function setRequest(Request $request): static
    {
        $this->request = $request;

        return $this;
    }

    public function get(string $id): mixed
    {
        return $this->container->get($id);
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function setContainer(ContainerInterface $container): mixed
    {
        $this->container = $container;

        return $this;
    }
}
