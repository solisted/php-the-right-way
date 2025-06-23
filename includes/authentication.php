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
