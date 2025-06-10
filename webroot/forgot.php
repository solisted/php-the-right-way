<?php
declare(strict_types=1);

require("../includes/errors.php");
require("../includes/authentication.php");
require("../includes/database.php");
require("../includes/request.php");
require("../includes/template.php");
require("../includes/validate.php");
require("../includes/sanitize.php");

function sl_render_forgot_password(array $reset, ?string $reset_message, bool $show_reset, array $errors): void
{
    require("../templates/forgot.php");
}

function sl_render_forgot_password_email(string $token): string
{
    ob_start();

    require("../templates/forgot_email.php");

    return ob_get_clean();
}

sl_request_methods_assert(["GET", "POST"]);

$reset = [
    "username" => "",
    "password" => "",
    "password1" => ""
];

$errors = [
    "username" => null,
    "password" => null,
    "password1" => null
];

$reset_message = null;
$show_reset = false;

$token = sl_request_query_get_string("token", "/^[0-9a-f]{32}$/", "");

$connection = sl_database_get_connection();

if (sl_request_is_method("GET") && $token !== "") {
    $statement = $connection->prepare("SELECT user_id FROM password_tokens WHERE token = :token AND created >= NOW() - INTERVAL 10 MINUTE");
    $statement->bindValue(":token", $token, PDO::PARAM_STR);
    $statement->execute();

    if ($statement->rowCount() !== 1) {
        sl_request_terminate(403);
    }

    $user_id = $statement->fetchColumn(0);
    $show_reset = true;
}

if (sl_request_is_method("POST") && sl_request_post_string_equals("action", "reset_password")) {
    print("Reset");
    exit();
}

if (sl_request_is_method("POST") && sl_request_post_string_equals("action", "request_link")) {
    $parameters = sl_request_get_post_parameters([
        "username" => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    ]);

    $reset["username"] = sl_sanitize_case($parameters["username"], MB_CASE_LOWER_SIMPLE);

    $errors["username"] = sl_validate_required($reset["username"], "Username");

    if (!sl_validate_has_errors($errors)) {
        $statement = $connection->prepare("SELECT id, email FROM users WHERE username = :username");
        $statement->bindValue(":username", $reset["username"], PDO::PARAM_STR);
        $statement->execute();

        if ($statement->rowCount() === 1) {
            $user = $statement->fetch(PDO::FETCH_ASSOC);

            $token = bin2hex(random_bytes(16));

            $statement = $connection->prepare("INSERT INTO password_tokens (user_id, token, created) VALUES (:user_id, :token, NOW())");
            $statement->bindValue(":user_id", $user["id"], PDO::PARAM_STR);
            $statement->bindValue(":token", $token, PDO::PARAM_STR);
            $statement->execute();

            $email_body = sl_render_forgot_password_email($token);

            mail($user["email"], "Password reset link", $email_body, ["From" => "ivan@solisted.net", "Content-Type" => "text/html"]);
        }

        $reset_message = "If username exists, password reset link will be sent to your inbox.";
    }
}

sl_template_render_header();
sl_render_forgot_password($reset, $reset_message, $show_reset, $errors);
sl_template_render_footer();
