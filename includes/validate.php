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
        return "${field_name} length must be between 2 and 32 characters";
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
        return "${field_name} must be a valid email address";
    }

    return null;
}

function sl_validate_rolename(string $name, string $field_name): ?string
{
    $length = mb_strlen($name);

    if ($length === 0) {
        return "${field_name} is required";
    }

    if ($length < 4 || $length > 32) {
        return "${field_name} length must be between 4 and 32 characters";
    }

    if (preg_match("/^[[:alpha:]]+$/u", $name) !== 1) {
        return "${field_name} can have only letters";
    }

    return null;
}

function sl_validate_description(string $description, string $field_name): ?string
{
    $length = mb_strlen($description);

    if ($length === 0) {
        return "${field_name} is required";
    }

    if ($length < 10 || $length > 1024) {
        return "${field_name} length must be between 10 and 1024 characters";
    }

    if (preg_match("/^[[:print:]]+$/u", $description) !== 1) {
        return "${field_name} can have only printable characters";
    }

    return null;
}

function sl_validate_actionname(string $name, string $field_name): ?string
{
    $length = mb_strlen($name);

    if ($length === 0) {
        return "${field_name} is required";
    }

    if ($length < 4 || $length > 32) {
        return "${field_name} length must be between 4 and 32 characters";
    }

    if (preg_match("/^[[:alpha:]]+$/u", $name) !== 1) {
        return "${field_name} can have only letters";
    }

    return null;
}

function sl_validate_login_username(string $username, string $field_name): ?string
{
    if (mb_strlen($username) === 0) {
        return "${field_name} is required";
    }

    return null;
}

function sl_validate_login_password(string $password, string $field_name): ?string
{
    if (mb_strlen($password) === 0) {
        return "${field_name} is required";
    }

    return null;
}

function sl_validate_categoryname(string $name, string $field_name): ?string
{
    $length = mb_strlen($name);

    if ($length === 0) {
        return "${field_name} is required";
    }

    if ($length < 4 || $length > 64) {
        return "${field_name} length must be between 4 and 64 characters";
    }

    if (preg_match("/^[[:alpha:][:space:]]+$/u", $name) !== 1) {
        return "${field_name} can have only letters and space character";
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

