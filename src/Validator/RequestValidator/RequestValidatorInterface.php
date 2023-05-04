<?php

namespace Dreamlabs\GraphQL\Validator\RequestValidator;


use Dreamlabs\GraphQL\Execution\Request;

interface RequestValidatorInterface
{

    public function validate(Request $request);

}
