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

function sl_render_action(array $action, array $errors): void
{
    require("../templates/action.php");
}

sl_request_methods_assert(["GET", "POST"]);

$action = [
    "id" => 0,
    "name" => "",
    "description" => ""
];
$errors = [
    "name" => null,
    "description" => null
];

$connection = sl_database_get_connection();

$action_id = sl_request_query_get_integer("id", 0, PHP_INT_MAX);

if (sl_request_is_method("GET")) {

    if ($action_id > 0) {
        sl_auth_assert_authorized_any(["ReadAction", "UpdateAction"]);

        $statement = $connection->prepare("SELECT id, name, description FROM actions WHERE id = :id");
        $statement->bindValue(":id", $action_id, PDO::PARAM_INT);
        $statement->execute();

        if ($statement->rowCount() !== 1) {
            sl_request_terminate(404);
        }

        $action = sl_template_escape_array($statement->fetch(PDO::FETCH_ASSOC));
    } else {
        sl_auth_assert_authorized("CreateAction");
    }
}

if (sl_request_is_method("POST")) {
    sl_auth_assert_authorized_any(["CreateAction", "UpdateAction"]);

    $parameters = sl_request_get_post_parameters([
        "name" => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        "description" => FILTER_SANITIZE_FULL_SPECIAL_CHARS
    ]);

    $action["id"] = $action_id;

    $action["name"] = sl_sanitize_trim($parameters["name"]);
    $action["description"] = sl_sanitize_trim($parameters["description"]);

    $errors["name"] = sl_validate_regexp($action["name"], 4, 32, "/^[[:alpha:]]+$/u", "Name", "letters");
    $errors["description"] = sl_validate_regexp($action["description"], 10, 1024, "/^[[:print:]]+$/u", "Description", "printable characters");

    if (!isset($errors["name"]) && !sl_database_is_unique_column($connection, "actions", "name", $action["name"], $action_id)) {
        $errors["name"] = "Action already exists";
    }

    if (!sl_validate_has_errors($errors)) {
        if ($action_id > 0) {
            sl_auth_assert_authorized("UpdateAction");
            $statement = $connection->prepare(
                "UPDATE actions SET name = :name, description = :description WHERE id = :id"
            );
            $statement->bindValue(":id", $action_id, PDO::PARAM_INT);
            sl_session_set_flash_message("Action updated successfully");
        } else {
            sl_auth_assert_authorized("CreateAction");
            $statement = $connection->prepare(
                "INSERT INTO actions (name, description) VALUES (:name, :description)"
            );
            sl_session_set_flash_message("Action created successfully");
        }

        $statement->bindValue(":name", $action["name"], PDO::PARAM_STR);
        $statement->bindValue(":description", $action["description"], PDO::PARAM_STR);
        $statement->execute();

        sl_request_redirect("/actions");
    }
}

sl_template_render_header();
sl_template_render_sidebar();
sl_render_action($action, $errors);
sl_template_render_footer();
