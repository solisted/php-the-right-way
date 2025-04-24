<?php
declare(strict_types=1);

function sl_validate_required(string $value, $field_name): ?string
{
    if (mb_strlen($value) === 0) {
        return "${field_name} is required";
    }

    return null;
}

function sl_validate_length(string $value, int $min_length, int $max_length, string $field_name): ?string
{
    $length = mb_strlen($value);

    if ($length === 0) {
        return "{$field_name} is required";
    }

    if ($length < $min_length || $length > $max_length) {
        return "{$field_name} length must be between {$min_length} and {$max_length} characters";
    }

    return null;
}

function sl_validate_regexp(string $value, int $min_length, int $max_length, string $regexp, string $field_name, string $regexp_message): ?string
{
    $length = mb_strlen($value);

    if ($length === 0) {
        return "{$field_name} is required";
    }

    if ($length < $min_length || $length > $max_length) {
        return "{$field_name} length must be between {$min_length} and {$max_length} characters";
    }

    if (preg_match($regexp, $value) !== 1) {
        return "{$field_name} can have only {$regexp_message}";
    }

    return null;
}

function sl_validate_email(string $email, string $field_name): ?string
{
    $length = mb_strlen($email);

    if ($length === 0) {
        return "${field_name} is required";
    }

    if ($length < 5 || $length > 128) {
        return "${field_name} length must be between 5 and 128 characters";
    }

    if (filter_var($email, FILTER_VALIDATE_EMAIL) !== $email) {
        return "${field_name} must be a valid email address";
    }

    return null;
}

function sl_validate_has_errors(array $errors): bool
{
    foreach ($errors as $error) {
        if ($error !== null) {
            return true;
        }
    }

    return false;
}

