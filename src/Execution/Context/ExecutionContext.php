<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/19/16 9:00 AM
*/

namespace Youshido\GraphQL\Execution\Context;


use Exception;
use Youshido\GraphQL\Execution\Container\ContainerInterface;
use Youshido\GraphQL\Execution\Request;
use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Introspection\Field\SchemaField;
use Youshido\GraphQL\Introspection\Field\TypeDefinitionField;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Validator\ErrorContainer\ErrorContainerTrait;
use Youshido\GraphQL\Validator\SchemaValidator\SchemaValidator;

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

    public function getField(AbstractObjectType $type, string $fieldName): Field
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
