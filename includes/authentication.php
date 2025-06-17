<?php
declare(strict_types=1);

session_start();

$user_id = isset($_SESSION["user_id"]) ? intval($_SESSION["user_id"]) : 0;

$non_authenticated_page = str_starts_with($_SERVER["REQUEST_URI"], "/login") ||
                          str_starts_with($_SERVER["REQUEST_URI"], "/forgot-password");

if ($user_id < 1 && !$non_authenticated_page) {
    header("Location: /login");
    exit();
}

$previous_csrf = isset($_SESSION["csrf"]) ? $_SESSION["csrf"] : null;
$current_csrf = bin2hex(random_bytes(16));
$_SESSION["csrf"] = $current_csrf;

function sl_auth_get_current_csrf(): string
{
    global $current_csrf;

    return $current_csrf;
}

function sl_auth_get_previous_csrf(): string
{
    global $previous_csrf;

    return $previous_csrf;
}

function sl_auth_assert_csrf(string $token): void
{
    if ($token !== sl_auth_get_previous_csrf()) {
        ob_end_clean();
        http_response_code(403);
        exit();
    }
}

function sl_auth_assert_csrf_is_valid(): void
{
    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] === "POST" &&
        (!isset($_POST["csrf"]) || $_POST["csrf"] !== sl_auth_get_previous_csrf())) {
        ob_end_clean();
        http_response_code(403);
        exit();
    }
}
