<?php
declare(strict_types=1);

require("../includes/errors.php");
require("../includes/database.php");
require("../includes/request.php");
require("../includes/template.php");
require("../includes/validate.php");
require("../includes/sanitize.php");

function sl_render_user(array $user, array $user_roles, array $other_roles, array $errors): void
{
    require("../templates/user.php");
}

function sl_user_get_user_roles(PDO $connection, int $user_id): array
{
    $statement = $connection->prepare("SELECT id, name, description FROM roles, users_roles WHERE role_id = id AND user_id = :user_id");
    $statement->bindValue(":user_id", $user_id, PDO::PARAM_INT);
    $statement->execute();

    return sl_template_escape_array_of_arrays($statement->fetchAll(PDO::FETCH_ASSOC));
}

function sl_user_get_other_roles(PDO $connection, int $user_id): array
{
    $statement = $connection->prepare(
        "SELECT id, name, description FROM roles WHERE id NOT IN (SELECT role_id FROM users_roles WHERE user_id = :user_id)"
    );
    $statement->bindValue(":user_id", $user_id, PDO::PARAM_INT);
    $statement->execute();

    return sl_template_escape_array_of_arrays($statement->fetchAll(PDO::FETCH_ASSOC));
}

sl_request_methods_assert(["GET", "POST"]);

$user = [
    "id" => 0,
    "username" => "",
    "first_name" => "",
    "last_name" => "",
    "email" => ""
];
$user_roles = [];
$other_roles = [];
$errors = [
    "username" => null,
    "first_name" => null,
    "last_name" => null,
    "email" => null
];

$connection = sl_database_get_connection();

if (sl_request_is_method("GET")) {
    $user_id = sl_request_query_get_integer("id", 0, PHP_INT_MAX);

    if ($user_id > 0) {
        $statement = $connection->prepare("SELECT id, username, first_name, last_name, email FROM users WHERE id = :id");
        $statement->bindValue(":id", $user_id, PDO::PARAM_INT);
        $statement->execute();

        if ($statement->rowCount() !== 1) {
            sl_request_terminate(404);
        }

        $user = sl_template_escape_array($statement->fetch(PDO::FETCH_ASSOC));

        $user_roles = sl_user_get_user_roles($connection, $user_id);
        $other_roles = sl_user_get_other_roles($connection, $user_id);
    }
} else {
    $role_id = sl_request_post_get_integer("role_id", 0, PHP_INT_MAX, 0);
    $user_id = sl_request_post_get_integer("id", 0, PHP_INT_MAX);

    if ($role_id === 0) {
        $parameters = sl_request_get_post_parameters([
            "username" => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            "first_name" => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            "last_name" => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            "email" => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        ]);

        $user["id"] = $user_id;

        $user["username"] = sl_sanitize_username($parameters["username"]);
        $user["first_name"] = sl_sanitize_name($parameters["first_name"]);
        $user["last_name"] = sl_sanitize_name($parameters["last_name"]);
        $user["email"] = sl_sanitize_email($parameters["email"]);

        $errors["username"] = sl_validate_username($user["username"], "Username");
        $errors["first_name"] = sl_validate_name($user["first_name"], "First name");
        $errors["last_name"] = sl_validate_name($user["last_name"], "Last name");
        $errors["email"] = sl_validate_email($user["email"], "Email");

        if (!isset($errors["username"]) && !sl_database_is_unique_username($connection, $user["username"], $user_id)) {
            $errors["username"] = "Username already exists";
        }

        if (!isset($errors["first_name"]) &&
            !isset($errors["last_name"]) &&
            !sl_database_is_unique_name($connection, $user["first_name"], $user["last_name"], $user_id)
        ) {
            $errors["first_name"] = "First name and last name already exist";
            $errors["last_name"] = "First name and last name already exist";
        }

        if (!isset($errors["email"]) && !sl_database_is_unique_email($connection, $user["email"], $user_id)) {
            $errors["email"] = "Email already exists";
        }

        if (!sl_validate_has_errors($errors)) {
            if ($user_id > 0) {
                $statement = $connection->prepare(
                    "UPDATE users SET username = :username, first_name = :first_name, last_name = :last_name, email = :email WHERE id = :id"
                );
                $statement->bindValue(":id", $user_id, PDO::PARAM_INT);
            } else {
                $statement = $connection->prepare(
                    "INSERT INTO users (username, first_name, last_name, email) VALUES (:username, :first_name, :last_name, :email)"
                );
            }

            $statement->bindValue(":username", $user["username"], PDO::PARAM_STR);
            $statement->bindValue(":first_name", $user["first_name"], PDO::PARAM_STR);
            $statement->bindValue(":last_name", $user["last_name"], PDO::PARAM_STR);
            $statement->bindValue(":email", $user["email"], PDO::PARAM_STR);
            $statement->execute();

            sl_request_redirect("/users");
        } else {
            $user_roles = sl_user_get_user_roles($connection, $user_id);
            $other_roles = sl_user_get_other_roles($connection, $user_id);
        }
    } else if ($user_id > 0) {
        $statement = $connection->prepare("INSERT INTO users_roles VALUES (:user_id, :role_id)");
        $statement->bindValue(":user_id", $user_id, PDO::PARAM_INT);
        $statement->bindValue(":role_id", $role_id, PDO::PARAM_INT);
        $statement->execute();

        sl_request_redirect("/user/${user_id}");
    } else {
        sl_request_terminate(400);
    }
}

sl_template_render_header();
sl_template_render_sidebar();
sl_render_user($user, $user_roles, $other_roles, $errors);
sl_template_render_footer();
