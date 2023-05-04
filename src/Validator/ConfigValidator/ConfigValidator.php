<?php

namespace Dreamlabs\GraphQL\Validator\ConfigValidator;


use Dreamlabs\GraphQL\Config\AbstractConfig;
use Dreamlabs\GraphQL\Exception\ConfigurationException;
use Dreamlabs\GraphQL\Exception\ValidationException;
use Dreamlabs\GraphQL\Validator\ConfigValidator\Rules\TypeValidationRule;
use Dreamlabs\GraphQL\Validator\ConfigValidator\Rules\ValidationRuleInterface;
use Dreamlabs\GraphQL\Validator\ErrorContainer\ErrorContainerTrait;

class ConfigValidator implements ConfigValidatorInterface
{

    use ErrorContainerTrait;

    protected array $rules = [];
    protected bool $extraFieldsAllowed = false;
    protected array $validationRules = [];
    protected static ConfigValidator $instance;

    private function __construct()
    {
        $this->initializeRules();
    }

    public static function getInstance(): ConfigValidator
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        self::$instance->clearErrors();

        return self::$instance;
    }
    
    /**
     * @throws ConfigurationException
     */
    public function assertValidConfig(AbstractConfig $config): void
    {
        if (!$this->isValidConfig($config)) {
            throw new ConfigurationException('Config is not valid for ' . ($config->getContextObject() ? $config->getContextObject()::class : null) . "\n" . implode("\n", $this->getErrorsArray(false)));
        }
    }

    public function isValidConfig(AbstractConfig $config): bool
    {
        return $this->validate($config->getData(), $this->getConfigFinalRules($config), $config->isExtraFieldsAllowed());
    }

    protected function getConfigFinalRules(AbstractConfig $config): array
    {
        $rules = $config->getRules();
        if ($config->isFinalClass()) {
            foreach ($rules as $name => $info) {
                if (!empty($info['final'])) {
                    $rules[$name]['required'] = true;
                }
            }
        }

        return $rules;
    }


    public function validate(array $data, array $rules = [], ?bool $allowExtraFields = null): bool
    {
        if ($allowExtraFields !== null) $this->setExtraFieldsAllowed($allowExtraFields);

        $processedFields = [];
        foreach ($rules as $fieldName => $fieldRules) {
            $processedFields[] = $fieldName;

            /** Custom validation of 'required' property */
            if (array_key_exists('required', $fieldRules)) {
                unset($fieldRules['required']);

                if (!array_key_exists($fieldName, $data)) {
                    $this->addError(new ValidationException(sprintf('Field "%s" is required', $fieldName)));

                    continue;
                }
            } elseif (!array_key_exists($fieldName, $data)) {
                continue;
            }
            if (!empty($fieldRules['final'])) unset($fieldRules['final']);

            /** Validation of all other rules*/
            foreach ($fieldRules as $ruleName => $ruleInfo) {
                if (!array_key_exists($ruleName, $this->validationRules)) {
                    $this->addError(new ValidationException(sprintf('Field "%s" has invalid rule "%s"', $fieldName, $ruleInfo)));

                    continue;
                }

                if (!$this->validationRules[$ruleName]->validate($data[$fieldName], $ruleInfo)) {
                    $this->addError(new ValidationException(sprintf('Field "%s" expected to be "%s" but got "%s"', $fieldName, $ruleName, gettype($data[$fieldName]))));
                }
            }
        }

        if (!$this->isExtraFieldsAllowed()) {
            foreach (array_keys($data) as $fieldName) {
                if (!in_array($fieldName, $processedFields)) {
                    $this->addError(new ValidationException(sprintf('Field "%s" is not expected', $fieldName)));
                }
            }
        }

        return $this->isValid();
    }

    protected function initializeRules(): void
    {
        $this->validationRules['type'] = new TypeValidationRule($this);
    }

    public function addRule($name, ValidationRuleInterface $rule): void
    {
        $this->validationRules[$name] = $rule;
    }

    public function isValid(): bool
    {
        return !$this->hasErrors();
    }

    public function isExtraFieldsAllowed(): bool
    {
        return $this->extraFieldsAllowed;
    }

    public function setExtraFieldsAllowed(bool $extraFieldsAllowed): static
    {
        $this->extraFieldsAllowed = $extraFieldsAllowed;

        return $this;
    }

}
