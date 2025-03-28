<?php
declare(strict_types=1);

require("../includes/errors.php");
require("../includes/database.php");
require("../includes/request.php");
require("../includes/template.php");
require("../includes/validate.php");
require("../includes/sanitize.php");

function sl_render_role(array $role, array $role_actions, array $other_actions, array $errors): void
{
    require("../templates/role.php");
}

function sl_role_get_role_actions(PDO $connection, int $role_id): array
{
    $statement = $connection->prepare("SELECT id, name, description FROM actions, roles_actions WHERE action_id = id AND role_id = :role_id");
    $statement->bindValue(":role_id", $role_id, PDO::PARAM_INT);
    $statement->execute();

    return sl_template_escape_array_of_arrays($statement->fetchAll(PDO::FETCH_ASSOC));
}

function sl_role_get_other_actions(PDO $connection, int $role_id): array
{
    $statement = $connection->prepare(
        "SELECT id, name, description FROM actions WHERE id NOT IN (SELECT action_id FROM roles_actions WHERE role_id = :role_id)"
    );
    $statement->bindValue(":role_id", $role_id, PDO::PARAM_INT);
    $statement->execute();

    return sl_template_escape_array_of_arrays($statement->fetchAll(PDO::FETCH_ASSOC));
}

sl_request_methods_assert(["GET", "POST"]);

$role = [
    "id" => 0,
    "name" => "",
    "description" => ""
];
$role_actions = [];
$other_actions = [];
$errors = [
    "name" => null,
    "description" => null
];

$connection = sl_database_get_connection();

if (sl_request_is_method("GET")) {
    $role_id = sl_request_query_get_integer("id", 0, PHP_INT_MAX);

    if ($role_id > 0) {
        $statement = $connection->prepare("SELECT id, name, description FROM roles WHERE id = :id");
        $statement->bindValue(":id", $role_id, PDO::PARAM_INT);
        $statement->execute();

        if ($statement->rowCount() !== 1) {
            sl_request_terminate(404);
        }

        $role = sl_template_escape_array($statement->fetch(PDO::FETCH_ASSOC));

        $role_actions = sl_role_get_role_actions($connection, $role_id);
        $other_actions = sl_role_get_other_actions($connection, $role_id);
    }
} else {
    $action_id = sl_request_post_get_integer("action_id", 0, PHP_INT_MAX, 0);
    $role_id = sl_request_post_get_integer("id", 0, PHP_INT_MAX);

    if ($action_id === 0) {
        $parameters = sl_request_get_post_parameters([
            "name" => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            "description" => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        ]);

        $role["id"] = $role_id;

        $role["name"] = sl_sanitize_rolename($parameters["name"]);
        $role["description"] = sl_sanitize_description($parameters["description"]);

        $errors["name"] = sl_validate_rolename($role["name"], "Name");
        $errors["description"] = sl_validate_description($role["description"], "Description");

        if (!isset($errors["name"]) && !sl_database_is_unique_rolename($connection, $role["name"], $role_id)) {
            $errors["name"] = "Role already exists";
        }

        if (!sl_validate_has_errors($errors)) {
            if ($role_id > 0) {
                $statement = $connection->prepare(
                    "UPDATE roles SET name = :name, description = :description WHERE id = :id"
                );
                $statement->bindValue(":id", $role_id, PDO::PARAM_INT);
            } else {
                $statement = $connection->prepare(
                    "INSERT INTO roles (name, description) VALUES (:name, :description)"
                );
            }

            $statement->bindValue(":name", $role["name"], PDO::PARAM_STR);
            $statement->bindValue(":description", $role["description"], PDO::PARAM_STR);
            $statement->execute();

            sl_request_redirect("/roles");
        } else {
            $role_actions = sl_role_get_role_actions($connection, $role_id);
            $other_actions = sl_role_get_other_actions($connection, $role_id);
        }
    } else if ($role_id > 0) {
        $statement = $connection->prepare("INSERT INTO roles_actions VALUES (:role_id, :action_id)");
        $statement->bindValue(":role_id", $role_id, PDO::PARAM_INT);
        $statement->bindValue(":action_id", $action_id, PDO::PARAM_INT);
        $statement->execute();

        sl_request_redirect("/role/${role_id}");
    } else {
        sl_request_terminate(400);
    }
}

sl_template_render_header();
sl_template_render_sidebar();
sl_render_role($role, $role_actions, $other_actions, $errors);
sl_template_render_footer();
