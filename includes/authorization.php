<?php
declare(strict_types=1);

function sl_auth_assert_session(): void
{
    if (!isset($_SESSION["actions"]) || !isset($_SESSION["user_id"]) || intval($_SESSION["user_id"]) < 1) {
        http_response_code(403);
        exit();
    }
}

function sl_auth_is_authorized(string $action): bool
{
    sl_auth_assert_session();

    return in_array($action, $_SESSION["actions"], true);
}

function sl_auth_is_authorized_any(array $actions): bool
{
    sl_auth_assert_session();

    return count(array_intersect($_SESSION["actions"], $actions)) >= 1;
}

function sl_auth_is_authorized_all(array $actions): bool
{
    sl_auth_assert_session();

    return count(array_intersect($_SESSION["actions"], $actions)) === count($actions);
}

function sl_auth_assert_authorized(string $action): void
{
    if (!sl_auth_is_authorized($action)) {
        http_response_code(403);
        exit();
    }
}

function sl_auth_assert_authorized_any(array $actions): void
{
    if (!sl_auth_is_authorized_any($actions)) {
        http_response_code(403);
        exit();
    }
}

function sl_auth_assert_authorized_all(array $actions): void
{
    if (!sl_auth_is_authorized_all($actions)) {
        http_response_code(403);
        exit();
    }
}
