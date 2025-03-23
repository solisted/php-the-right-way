<?php
declare(strict_types=1);

function sl_validate_username(string $username, string $field_name): ?string
{
    $length = mb_strlen($username);

    if ($length === 0) {
        return "${field_name} is required";
    }

    if ($length < 6 || $length > 16) {
        return "${field_name} length must be between 6 and 16 characters";
    }

    if (preg_match("/^[[:alnum:]]+$/u", $username) !== 1) {
        return "${field_name} can have only alphanumeric characters";
    }

    return null;
}

function sl_validate_name(string $name, string $field_name): ?string
{
    $length = mb_strlen($name);

    if ($length === 0) {
        return "${field_name} is required";
    }

    if ($length < 2 || $length > 32) {
        return "${field_name} length must be between 6 and 16 characters";
    }

    if (preg_match("/^[[:alpha:]]+$/u", $name) !== 1) {
        return "${field_name} can have only letters";
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
        return "${field_name} must a valid email address";
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
