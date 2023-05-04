<?php

namespace Dreamlabs\GraphQL\Introspection\Traits;

use Dreamlabs\GraphQL\Type\AbstractType;
use Dreamlabs\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\TypeMap;
use Dreamlabs\GraphQL\Type\Union\AbstractUnionType;

trait TypeCollectorTrait
{
    
    protected array $types = [];
    
    protected function collectTypes(AbstractType $type): void
    {
        if (array_key_exists($type->getName(), $this->types)) {
            return;
        }
        
        switch ($type->getKind()) {
            case TypeMap::KIND_INTERFACE:
            case TypeMap::KIND_UNION:
            case TypeMap::KIND_ENUM:
            case TypeMap::KIND_SCALAR:
                $this->insertType($type->getName(), $type);
                
                if ($type->getKind() == TypeMap::KIND_UNION) {
                    /** @var AbstractUnionType $type */
                    foreach ($type->getTypes() as $subType) {
                        $this->collectTypes($subType);
                    }
                }
                
                break;
            
            case TypeMap::KIND_INPUT_OBJECT:
            case TypeMap::KIND_OBJECT:
                /** @var AbstractObjectType $namedType */
                $namedType = $type->getNamedType();
                $this->checkAndInsertInterfaces($namedType);
                
                if ($this->insertType($namedType->getName(), $namedType)) {
                    $this->collectFieldsArgsTypes($namedType);
                }
                
                break;
            
            case TypeMap::KIND_NON_NULL:
            case TypeMap::KIND_LIST:
                $this->collectTypes($type->getNamedType());
                break;
        }
    }
    
    private function checkAndInsertInterfaces($type): void
    {
        foreach ((array)$type->getConfig()->getInterfaces() as $interface) {
            $this->insertType($interface->getName(), $interface);
            
            if ($interface instanceof AbstractInterfaceType) {
                foreach ($interface->getImplementations() as $implementation) {
                    $this->insertType($implementation->getName(), $implementation);
                }
            }
        }
    }
    
    private function collectFieldsArgsTypes(AbstractObjectType $type): void
    {
        foreach ($type->getConfig()->getFields() as $field) {
            $arguments = $field->getConfig()->getArguments();
            
            if (is_array($arguments)) {
                foreach ($arguments as $argument) {
                    $this->collectTypes($argument->getType());
                }
            }
            
            $this->collectTypes($field->getType());
        }
    }
    
    private function insertType($name, $type): bool
    {
        if ( ! array_key_exists($name, $this->types)) {
            $this->types[$name] = $type;
            
            return true;
        }
        
        return false;
    }
    
}
