<?php

namespace Dreamlabs\GraphQL\Execution\Context;


use Dreamlabs\GraphQL\Execution\Container\ContainerInterface;
use Dreamlabs\GraphQL\Execution\Request;
use Dreamlabs\GraphQL\Schema\AbstractSchema;
use Dreamlabs\GraphQL\Validator\ErrorContainer\ErrorContainerInterface;

interface ExecutionContextInterface extends ErrorContainerInterface
{

    public function getSchema(): AbstractSchema;

    public function setSchema(AbstractSchema $schema): static;

    public function getRequest(): Request;

    public function setRequest(Request $request): static;

    public function getContainer(): ContainerInterface;

    public function setContainer(ContainerInterface $container): mixed;

}
