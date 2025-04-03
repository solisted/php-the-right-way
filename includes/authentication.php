<?php
declare(strict_types=1);

session_start();

$user_id = isset($_SESSION["user_id"]) ? intval($_SESSION["user_id"]) : 0;

if ($user_id < 1 && $_SERVER["REQUEST_URI"] !== "/login") {
    header("Location: /login");
    exit();
}
