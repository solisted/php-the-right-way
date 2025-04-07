<?php
declare(strict_types=1);

require("../includes/errors.php");
require("../includes/authentication.php");
require("../includes/database.php");
require("../includes/request.php");
require("../includes/template.php");
require("../includes/validate.php");
require("../includes/sanitize.php");

function sl_render_login(string $username, string $password, ?string $auth_error, array $errors): void
{
    require("../templates/login.php");
}

sl_request_methods_assert(["GET", "POST"]);

$username = "";
$password = "";
$errors = [
    "username" => null,
    "password" => null
];
$auth_error = null;

if (sl_request_is_method("POST")) {
    $parameters = sl_request_get_post_parameters([
        "username" => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        "password" => FILTER_SANITIZE_FULL_SPECIAL_CHARS
    ]);

    $username = sl_sanitize_username($parameters["username"]);
    $password = sl_sanitize_password($parameters["password"]);

    $errors["username"] = sl_validate_login_username($username, "Username");
    $errors["password"] = sl_validate_login_password($password, "Password");

    if (!sl_validate_has_errors($errors)) {
        $connection = sl_database_get_connection();

        $statement = $connection->prepare("SELECT id, password FROM users WHERE username = :username");
        $statement->bindValue(":username", $username, PDO::PARAM_STR);
        $statement->execute();

        if ($statement->rowCount() !== 1) {
            $auth_error = "Incorrect username or password";
        } else {
            $user = $statement->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $user["password"]) === true) {
                $statement = $connection->prepare(
                    "SELECT DISTINCT a.name FROM users u, users_roles ur, roles_actions ra, actions a WHERE u.id = ur.user_id AND ur.role_id = ra.role_id AND a.id = ra.action_id AND u.id = :user_id"
                );
                $statement->bindValue(":user_id", $user["id"], PDO::PARAM_INT);
                $statement->execute();

                $actions = $statement->fetchAll(PDO::FETCH_COLUMN, 0);

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["actions"] = $actions;

                sl_request_redirect("/users");
            } else {
                $auth_error = "Incorrect username or password";
            }
        }
    }
}

sl_template_render_header();
sl_render_login($username, $password, $auth_error, $errors);
sl_template_render_footer();
