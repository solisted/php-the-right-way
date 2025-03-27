<?php
declare(strict_types=1);

function sl_sanitize_username(string $username): string
{
    return mb_strtolower(mb_trim($username));
}

function sl_sanitize_name(string $name): string
{
    return mb_convert_case(mb_trim($name), MB_CASE_TITLE_SIMPLE);
}

function sl_sanitize_email(string $email): string
{
    return mb_strtolower(mb_trim($email));
}

function sl_sanitize_rolename(string $name): string
{
    return mb_convert_case(mb_trim($name), MB_CASE_TITLE_SIMPLE);
}

function sl_sanitize_description(string $description): string
{
    return mb_trim($description);
}

function sl_sanitize_actionname(string $name): string
{
    return mb_trim($name);
}
