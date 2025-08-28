<?php
declare(strict_types=1);

require("../config/config.php");
require("../includes/errors.php");
require("../includes/authentication.php");
require("../includes/authorization.php");
require("../includes/database.php");
require("../includes/request.php");
require("../includes/template.php");
require("../includes/validate.php");
require("../includes/sanitize.php");
require("../includes/session.php");

function sl_render_user(
    int $tab_number,
    array $user,
    array $user_roles,
    array $other_roles,
    array $user_history,
    array $statuses,
    array $errors): void
{
    require("../templates/user.php");
}

function sl_user_get_user_by_id(PDO $connection, int $user_id): array
{
    $statement = $connection->prepare(
        "SELECT
             u.id, u.username, u.first_name, u.last_name, u.email,
             SUBSTRING_INDEX(GROUP_CONCAT(uh.id ORDER BY uh.created DESC), ',', 1) AS status_history_id,
             SUBSTRING_INDEX(GROUP_CONCAT(uh.status_id ORDER BY uh.created DESC), ',', 1) AS status_id
         FROM users u
         LEFT JOIN user_history uh ON (uh.user_id = u.id)
         LEFT JOIN user_statuses us ON (uh.status_id = us.id)
         WHERE u.id = :id
         GROUP BY u.id"
    );
    $statement->bindValue(":id", $user_id, PDO::PARAM_INT);
    $statement->execute();

    if ($statement->rowCount() !== 1) {
        return [];
    }

    return sl_template_escape_array($statement->fetch(PDO::FETCH_ASSOC));
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
        "SELECT id, name FROM roles LEFT JOIN users_roles ON role_id = id AND user_id = :user_id WHERE user_id IS NULL"
    );
    $statement->bindValue(":user_id", $user_id, PDO::PARAM_INT);
    $statement->execute();

    return sl_template_escape_array_of_arrays($statement->fetchAll(PDO::FETCH_ASSOC));
}

function sl_user_get_user_history(PDO $connection, int $user_id): array
{
    $statement = $connection->prepare("SELECT uh.id, uh.status_id, us.name, uh.created FROM user_history uh LEFT JOIN user_statuses us ON (uh.status_id = us.id) WHERE user_id = :user_id ORDER BY uh.created DESC");
    $statement->bindValue(":user_id", $user_id, PDO::PARAM_INT);
    $statement->execute();

    return sl_template_escape_array_of_arrays($statement->fetchAll(PDO::FETCH_ASSOC));
}

function sl_user_change_user_status(PDO $connection, int $user_id, int $status_id)
{
    $statement = $connection->prepare("INSERT INTO user_history (user_id, status_id, created) VALUES (:user_id, :status_id, NOW())");
    $statement->bindValue(":user_id", $user_id, PDO::PARAM_INT);
    $statement->bindValue(":status_id", $status_id, PDO::PARAM_INT);
    $statement->execute();
}

sl_request_methods_assert(["GET", "POST"]);

$user = [
    "id" => 0,
    "username" => "",
    "first_name" => "",
    "last_name" => "",
    "email" => "",
    "password" => "",
    "password1" => "",
    "status_id" => SL_USER_INVALID_STATUS_ID,
    "status_history_id" => 0
];
$user_roles = [];
$other_roles = [];
$user_history = [];
$errors = [
    "username" => null,
    "first_name" => null,
    "last_name" => null,
    "email" => null,
    "password" => null,
    "password1" => null
];

$connection = sl_database_get_connection();
$statuses = sl_template_escape_array_of_arrays(sl_database_get_user_statuses($connection));

$user_id = sl_request_query_get_integer("id", 0, PHP_INT_MAX);
$tab_number = sl_request_query_get_integer("tab", 0, 1, 0);

if (sl_request_is_method("GET") && $user_id > 0) {
    sl_auth_assert_authorized_any(["ReadUser", "UpdateUser"]);

    $user = sl_user_get_user_by_id($connection, $user_id);
    if (empty($user)) {
        sl_request_terminate(404);
    }

    $user["password"] = "";
    $user["password1"] = "";

    if ($tab_number === 0) {
        $user_roles = sl_user_get_user_roles($connection, $user_id);
        $other_roles = sl_user_get_other_roles($connection, $user_id);
    } elseif ($tab_number == 1) {
        $user_history = sl_user_get_user_history($connection, $user_id);
    }
} elseif (sl_request_is_method("GET") && $user_id === 0) {
    sl_auth_assert_authorized("CreateUser");
}


if (sl_request_is_method("POST") && sl_request_post_string_equals("action", "add_update_user")) {
    sl_auth_assert_authorized_any(["CreateUser", "UpdateUser"]);

    $user_id = sl_request_post_get_integer("id", 0, PHP_INT_MAX);

    if ($user_id > 0) {
        $existing_user = sl_user_get_user_by_id($connection, $user_id);
        if (empty($existing_user)) {
            sl_request_terminate(404);
        }
    }

    $parameters = sl_request_get_post_parameters([
        "username" => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        "first_name" => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        "last_name" => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        "email" => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        "password" => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        "password1" => FILTER_SANITIZE_FULL_SPECIAL_CHARS
    ]);

    $user["id"] = $user_id;
    $user["status_id"] = $user_id > 0 ? $existing_user["status_id"] : SL_USER_INVALID_STATUS_ID;
    $user["status_history_id"] = $user_id > 0 ? $existing_user["status_history_id"] : SL_USER_ACTIVE_STATUS_ID;

    $user["username"] = sl_sanitize_case($parameters["username"], MB_CASE_LOWER_SIMPLE);
    $user["first_name"] = sl_sanitize_case($parameters["first_name"], MB_CASE_TITLE_SIMPLE);
    $user["last_name"] = sl_sanitize_case($parameters["last_name"], MB_CASE_TITLE_SIMPLE);
    $user["email"] = sl_sanitize_case($parameters["email"], MB_CASE_LOWER_SIMPLE);
    $user["password"] = sl_sanitize_trim($parameters["password"]);
    $user["password1"] = sl_sanitize_trim($parameters["password1"]);

    $errors["username"] = sl_validate_regexp($user["username"], 6, 16, "/^[[:alnum:]]+$/u", "Username", "alphanumeric characters");
    $errors["first_name"] = sl_validate_regexp($user["first_name"], 2, 32, "/^[[:alpha:]]+$/u", "First name", "letters");
    $errors["last_name"] = sl_validate_regexp($user["last_name"], 2, 32, "/^[[:alpha:]]+$/u", "Last name", "letters");
    $errors["email"] = sl_validate_email($user["email"], "Email");

    $update_password = false;

    if ($user_id === 0 || ($user_id > 0 && !empty($user["password"]))) {
        $errors["password"] = sl_validate_length($user["password"], 8, 32, "Password");

        if ($errors["password"] === null && $user["password"] !== $user["password1"]) {
            $errors["password1"] = "Passwords do not match";
        } else {
            $update_password = true;
        }
    }

    if (!isset($errors["username"]) && !sl_database_is_unique_column($connection, "users", "username", $user["username"], $user_id)) {
        $errors["username"] = "Username already exists";
    }

    if (!isset($errors["first_name"]) &&
        !isset($errors["last_name"]) &&
        !sl_database_is_unique_user_name($connection, $user["first_name"], $user["last_name"], $user_id)
    ) {
        $errors["first_name"] = "First name and last name already exist";
        $errors["last_name"] = "First name and last name already exist";
    }

    if (!isset($errors["email"]) && !sl_database_is_unique_column($connection, "users", "email", $user["email"], $user_id)) {
        $errors["email"] = "Email already exists";
    }

    if (!sl_validate_has_errors($errors)) {
        $connection->beginTransaction();

        if ($user_id > 0) {
            sl_auth_assert_authorized("UpdateUser");

            if ($update_password) {
                $statement = $connection->prepare(
                    "UPDATE users SET username = :username, first_name = :first_name, last_name = :last_name, email = :email, password = :password WHERE id = :id"
                );
                $statement->bindValue(":password", password_hash($user["password"], PASSWORD_BCRYPT), PDO::PARAM_STR);
            } else {
                $statement = $connection->prepare(
                    "UPDATE users SET username = :username, first_name = :first_name, last_name = :last_name, email = :email WHERE id = :id"
                );
            }

            $statement->bindValue(":id", $user_id, PDO::PARAM_INT);
        } else {
            sl_auth_assert_authorized("CreateUser");

            $statement = $connection->prepare(
                "INSERT INTO users (username, first_name, last_name, email, password) VALUES (:username, :first_name, :last_name, :email, :password)"
            );

            $statement->bindValue(":password", password_hash($user["password"], PASSWORD_BCRYPT), PDO::PARAM_STR);
        }

        $statement->bindValue(":username", $user["username"], PDO::PARAM_STR);
        $statement->bindValue(":first_name", $user["first_name"], PDO::PARAM_STR);
        $statement->bindValue(":last_name", $user["last_name"], PDO::PARAM_STR);
        $statement->bindValue(":email", $user["email"], PDO::PARAM_STR);
        $statement->execute();

        if ($user_id === 0) {
            $user_id = intval($connection->lastInsertId());

            sl_user_change_user_status($connection, $user_id, SL_USER_ACTIVE_STATUS_ID);
        }

        $connection->commit();

        sl_session_set_flash_message($user_id > 0 ? "User updated successfully" : "User added successfully");
        sl_request_redirect("/users");
    } else {
        if ($tab_number === 0) {
            $user_roles = sl_user_get_user_roles($connection, $user_id);
            $other_roles = sl_user_get_other_roles($connection, $user_id);
        } elseif ($tab_number == 1) {
            $user_history = sl_user_get_user_history($connection, $user_id);
        }
    }
}

if (sl_request_is_method("POST") && sl_request_post_string_equals("action", "add_role")) {
    sl_auth_assert_authorized("UpdateUser");

    $user_id = sl_request_post_get_integer("id", 0, PHP_INT_MAX);
    $role_id = sl_request_post_get_integer("role_id", 0, PHP_INT_MAX, 0);

    $statement = $connection->prepare("INSERT INTO users_roles VALUES (:user_id, :role_id)");
    $statement->bindValue(":user_id", $user_id, PDO::PARAM_INT);
    $statement->bindValue(":role_id", $role_id, PDO::PARAM_INT);
    $statement->execute();

    sl_session_set_flash_message("Role added to the user successfully");
    sl_request_redirect("/user/${user_id}");
}

if (sl_request_is_method("POST") && sl_request_post_string_equals("action", "delete_role")) {
    sl_auth_assert_authorized("UpdateUser");

    $user_id = sl_request_post_get_integer("id", 0, PHP_INT_MAX);
    $role_id = sl_request_post_get_integer("role_id", 0, PHP_INT_MAX, 0);

    $statement = $connection->prepare("DELETE FROM users_roles WHERE user_id = :user_id AND role_id = :role_id");
    $statement->bindValue(":user_id", $user_id, PDO::PARAM_INT);
    $statement->bindValue(":role_id", $role_id, PDO::PARAM_INT);
    $statement->execute();

    sl_session_set_flash_message("Role deleted from the user successfully");
    sl_request_redirect("/user/${user_id}");
}

if (sl_request_is_method("POST") && sl_request_post_string_equals("action", "lock_user")) {
    sl_auth_assert_authorized("UpdateUser");

    $user_id = sl_request_post_get_integer("id", 0, PHP_INT_MAX);

    $user = sl_user_get_user_by_id($connection, $user_id);
    if (empty($user)) {
        sl_request_terminate(404);
    }

    if ($user["status_id"] != SL_USER_ACTIVE_STATUS_ID) {
        sl_request_terminate(400);
    }

    sl_user_change_user_status($connection, $user_id, SL_USER_LOCKED_STATUS_ID);

    sl_session_set_flash_message("User successfully locked");
    if (sl_request_post_string_equals("return", "users")) {
        sl_request_redirect("/users");
    }
    sl_request_redirect("/user/${user_id}");
}

if (sl_request_is_method("POST") && sl_request_post_string_equals("action", "unlock_user")) {
    sl_auth_assert_authorized("UpdateUser");

    $user_id = sl_request_post_get_integer("id", 0, PHP_INT_MAX);

    $user = sl_user_get_user_by_id($connection, $user_id);
    if (empty($user)) {
        sl_request_terminate(404);
    }

    if ($user["status_id"] != SL_USER_LOCKED_STATUS_ID) {
        sl_request_terminate(400);
    }

    sl_user_change_user_status($connection, $user_id, SL_USER_ACTIVE_STATUS_ID);

    sl_session_set_flash_message("User successfully unlocked");
    if (sl_request_post_string_equals("return", "users")) {
        sl_request_redirect("/users");
    }
    sl_request_redirect("/user/${user_id}");
}

if (sl_request_is_method("POST") && sl_request_post_string_equals("action", "delete_user")) {
    sl_auth_assert_authorized("UpdateUser");

    $user_id = sl_request_post_get_integer("id", 0, PHP_INT_MAX);

    $user = sl_user_get_user_by_id($connection, $user_id);
    if (empty($user)) {
        sl_request_terminate(404);
    }

    if ($user["status_id"] != SL_USER_ACTIVE_STATUS_ID) {
        sl_request_terminate(400);
    }

    sl_user_change_user_status($connection, $user_id, SL_USER_DELETED_STATUS_ID);

    sl_session_set_flash_message("User successfully deleted");
    if (sl_request_post_string_equals("return", "users")) {
        sl_request_redirect("/users");
    }
    sl_request_redirect("/user/${user_id}");
}

if (sl_request_is_method("POST") && sl_request_post_string_equals("action", "restore_user")) {
    sl_auth_assert_authorized("UpdateUser");

    $user_id = sl_request_post_get_integer("id", 0, PHP_INT_MAX);

    $user = sl_user_get_user_by_id($connection, $user_id);
    if (empty($user)) {
        sl_request_terminate(404);
    }

    if ($user["status_id"] != SL_USER_DELETED_STATUS_ID) {
        sl_request_terminate(400);
    }

    sl_user_change_user_status($connection, $user_id, SL_USER_ACTIVE_STATUS_ID);

    sl_session_set_flash_message("User successfully restored");
    if (sl_request_post_string_equals("return", "users")) {
        sl_request_redirect("/users");
    }
    sl_request_redirect("/user/${user_id}");
}

sl_template_render_header();
sl_template_render_sidebar();
sl_render_user($tab_number, $user, $user_roles, $other_roles, $user_history, $statuses, $errors);
sl_template_render_footer();
