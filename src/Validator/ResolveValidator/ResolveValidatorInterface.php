<?php

namespace Dreamlabs\GraphQL\Validator\ResolveValidator;


use Dreamlabs\GraphQL\Execution\Request;
use Dreamlabs\GraphQL\Field\FieldInterface;
use Dreamlabs\GraphQL\Parser\Ast\Interfaces\FieldInterface as AstFieldInterface;
use Dreamlabs\GraphQL\Type\AbstractType;
use Dreamlabs\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Dreamlabs\GraphQL\Type\Union\AbstractUnionType;

interface ResolveValidatorInterface
{

    public function assetTypeHasField(AbstractType $objectType, AstFieldInterface $ast);

    public function assertValidArguments(FieldInterface $field, AstFieldInterface $query, Request $request);

    public function assertValidResolvedValueForField(FieldInterface $field, $resolvedValue);

    public function assertTypeImplementsInterface(AbstractType $type, AbstractInterfaceType $interface);

    public function assertTypeInUnionTypes(AbstractType $type, AbstractUnionType $unionType);
}
