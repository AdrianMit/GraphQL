<?php
/**
 * Date: 5/20/16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Execution\Context;


use Youshido\GraphQL\Execution\Container\ContainerInterface;
use Youshido\GraphQL\Execution\Request;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Validator\ErrorContainer\ErrorContainerInterface;

interface ExecutionContextInterface extends ErrorContainerInterface
{

    public function getSchema(): AbstractSchema;

    public function setSchema(AbstractSchema $schema): static;

    public function getRequest(): Request;

    public function setRequest(Request $request): static;

    public function getContainer(): ContainerInterface;

    public function setContainer(ContainerInterface $container): mixed;

}
