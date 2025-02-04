<?php

namespace App\Core;

class Validator
{
    private array $errors = [];
    private array $data;

    public function validate(array $data, array $rules): bool
    {
        $this->data = $data;
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {
            foreach ($fieldRules as $rule) {
                if (strpos($rule, ':') !== false) {
                    [$ruleName, $parameter] = explode(':', $rule);
                    $this->validateFieldWithParameter($field, $ruleName, $parameter);
                } else {
                    $this->validateField($field, $rule);
                }
            }
        }

        return empty($this->errors);
    }

    private function validateField(string $field, string $rule): void
    {
        $value = $this->data[$field] ?? '';

        switch ($rule) {
            case 'required':
                if (empty($value)) {
                    $this->addError($field, 'Field is required');
                }
                break;

            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, 'Invalid email format');
                }
                break;
        }
    }

    private function validateFieldWithParameter(string $field, string $rule, string $parameter): void
    {
        $value = $this->data[$field] ?? '';

        switch ($rule) {
            case 'min':
                if (strlen($value) < (int)$parameter) {
                    $this->addError($field, "Minimum length is $parameter characters");
                }
                break;
        }
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
