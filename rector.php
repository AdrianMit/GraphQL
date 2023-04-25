<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Symfony\Set\SymfonyLevelSetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddMethodCallBasedStrictParamTypeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddParamTypeDeclarationRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddReturnTypeDeclarationRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromReturnNewRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictTypedCallRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictTypedPropertyRector;
use Rector\TypeDeclaration\Rector\Param\ParamTypeFromStrictTypedPropertyRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;
use RectorPrefix202304\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

//use Rector\Nette\Set\NetteSetList;
//use Rector\TypeDeclaration\Rector\ClassMethod\AddArrayParamDocTypeRector;
//use Rector\TypeDeclaration\Rector\ClassMethod\AddArrayReturnDocTypeRector;
//use Rector\TypeDeclaration\Rector\FunctionLike\ReturnTypeDeclarationRector;

return static function (ContainerConfigurator $containerConfigurator): void {
    // get parameters
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::AUTO_IMPORT_NAMES, true);
    $parameters->set(Option::PATHS, [
        //__DIR__.'/src',
        __DIR__.'/tests',
    ]);
    
    // Define what rule sets will be applied
    $containerConfigurator->import(LevelSetList::UP_TO_PHP_80);
    //    $containerConfigurator->import(DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES);
    //    $containerConfigurator->import(SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES);
    //    $containerConfigurator->import(NetteSetList::ANNOTATIONS_TO_ATTRIBUTES);
    //    $containerConfigurator->import(SensiolabsSetList::FRAMEWORK_EXTRA_61);
    $containerConfigurator->import(SymfonyLevelSetList::UP_TO_SYMFONY_44);
    
    // get services (needed for register a single rule)
    $services = $containerConfigurator->services();
    
    // register a single rule
    // $services->set(TypedPropertyRector::class);
    //    $services->set(AnnotationToAttributeRector::class)->configure(
    //        [
    //            new AnnotationToAttribute(JMS\Serializer\Annotation\Type::class),
    //            new AnnotationToAttribute(JMS\Serializer\Annotation\Groups::class),
    //            new AnnotationToAttribute(JMS\Serializer\Annotation\SerializedName::class),
    //            new AnnotationToAttribute(JMS\Serializer\Annotation\Exclude::class),
    //            new AnnotationToAttribute(JMS\Serializer\Annotation\MaxDepth::class),
    //            new AnnotationToAttribute(JMS\Serializer\Annotation\ExclusionPolicy::class),
    //        ]
    //    );
    
    $services->set(ParamTypeFromStrictTypedPropertyRector::class);
    $services->set(ReturnTypeFromReturnNewRector::class);
    $services->set(ReturnTypeFromStrictTypedPropertyRector::class);
    $services->set(ReturnTypeFromStrictTypedCallRector::class);
    $services->set(TypedPropertyFromStrictConstructorRector::class);
    //$services->set(AddArrayParamDocTypeRector::class);
    //$services->set(AddArrayReturnDocTypeRector::class);
    $services->set(AddMethodCallBasedStrictParamTypeRector::class);
    $services->set(AddParamTypeDeclarationRector::class);
    $services->set(AddReturnTypeDeclarationRector::class);
    $services->set(AddVoidReturnTypeWhereNoReturnRector::class);
    //$services->set(ReturnTypeDeclarationRector::class);
    
    //    $parameters->set(Option::SKIP, [
    //        ClassPropertyAssignToConstructorPromotionRector::class => [
    //            __DIR__ . '/src/DTO',
    //            __DIR__ . '/src/Entity',
    //            __DIR__ . '/src/Request',
    //            __DIR__ . '/src/Response',
    //            __DIR__ . '/src/Operation',
    //        ]
    //    ]);
};
