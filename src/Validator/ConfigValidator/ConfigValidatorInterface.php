<?php

namespace Dreamlabs\GraphQL\Validator\ConfigValidator;


interface ConfigValidatorInterface
{

    public function validate(array $data, array $rules = [], bool $allowExtraFields = null);

}
