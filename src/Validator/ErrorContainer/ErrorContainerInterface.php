<?php

namespace Dreamlabs\GraphQL\Validator\ErrorContainer;

use Exception;
interface ErrorContainerInterface
{

    public function addError(Exception $exception);

    public function mergeErrors(ErrorContainerInterface $errorContainer);

    public function hasErrors();

    public function getErrors();

    public function getErrorsArray();

    public function clearErrors();

}
