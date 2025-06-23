<?php

namespace App\Utils;

class Validator
{
    private array $data;
    private array $rules;
    private array $errors = [];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public function validate(): bool
    {
        foreach ($this->rules as $field => $rules) {
            $value = $this->data[$field] ?? null;
            $fieldRules = explode('|', $rules);

            foreach ($fieldRules as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function applyRule(string $field, mixed $value, string $rule): void
    {
        $ruleParts = explode(':', $rule);
        $ruleName = $ruleParts[0];
        $ruleValue = $ruleParts[1] ?? null;

        switch ($ruleName) {
            case 'required':
                if (empty($value)) {
                    $this->addError($field, 'Поле обов\'язкове для заповнення');
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, 'Некоректна адреса електронної пошти');
                }
                break;

            case 'min':
                if (!empty($value) && strlen($value) < (int)$ruleValue) {
                    $this->addError($field, "Мінімальна довжина $ruleValue символів");
                }
                break;

            case 'max':
                if (!empty($value) && strlen($value) > (int)$ruleValue) {
                    $this->addError($field, "Максимальна довжина $ruleValue символів");
                }
                break;

            case 'alpha_num':
                if (!empty($value) && !preg_match('/^[a-zA-Z0-9]+$/', $value)) {
                    $this->addError($field, 'Дозволені тільки латинські літери та цифри');
                }
                break;

            case 'password':
                if (!empty($value) && !$this->validatePassword($value)) {
                    $this->addError($field, 'Пароль повинен містити великі та малі літери, цифри (мінімум 6 символів)');
                }
                break;

            case 'phone':
                if (!empty($value) && !$this->validatePhone($value)) {
                    $this->addError($field, 'Некоректний номер телефону');
                }
                break;
        }
    }

    private function validatePassword(string $password): bool
    {
        return strlen($password) >= 6
            && preg_match('/[a-z]/', $password)
            && preg_match('/[A-Z]/', $password)
            && preg_match('/[0-9]/', $password);
    }

    private function validatePhone(string $phone): bool
    {
        return preg_match('/^(\+\d{1,3}[-.\s]?)?\(?\d{1,4}\)?[-.\s]?\d{1,4}[-.\s]?\d{1,4}$/', $phone);
    }

    private function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }
}