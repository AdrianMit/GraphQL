<?php

namespace Dreamlabs\GraphQL\Validator\ConfigValidator\Rules;


interface ValidationRuleInterface
{
    public function validate($data, $ruleInfo);
}
