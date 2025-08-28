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

function sl_render_role(
    int $tab_number,
    array $role,
    array $role_actions,
    array $other_actions,
    array $role_history,
    array $statuses,
    array $errors,
    string $url,
    int $page,
    int $size,
    int $total_pages): void
{
    require("../templates/role.php");
}

function sl_role_get_role_by_id(PDO $connection, int $role_id): array
{
    $statement = $connection->prepare(
        "SELECT
             r.id, r.name, r.description,
             SUBSTRING_INDEX(GROUP_CONCAT(rh.id ORDER BY rh.created DESC), ',', 1) AS status_history_id,
             SUBSTRING_INDEX(GROUP_CONCAT(rh.status_id ORDER BY rh.created DESC), ',', 1) AS status_id
         FROM roles r
         LEFT JOIN role_history rh ON (rh.role_id = r.id)
         LEFT JOIN role_statuses rs ON (rh.status_id = rs.id)
         WHERE r.id = :id
         GROUP BY r.id"
    );
    $statement->bindValue(":id", $role_id, PDO::PARAM_INT);
    $statement->execute();

    if ($statement->rowCount() !== 1) {
        return [];
    }

    return sl_template_escape_array($statement->fetch(PDO::FETCH_ASSOC));
}

function sl_role_get_role_actions_count(PDO $connection, int $role_id, int $page, int $page_size): int
{
    $statement = $connection->prepare("SELECT COUNT(*) FROM actions, roles_actions WHERE action_id = id AND role_id = :role_id");
    $statement->bindValue(":role_id", $role_id, PDO::PARAM_INT);
    $statement->execute();

    return intval($statement->fetchColumn(0));
}

function sl_role_get_role_actions(PDO $connection, int $role_id, int $page, int $page_size): array
{
    $statement = $connection->prepare("SELECT id, name, description FROM actions, roles_actions WHERE action_id = id AND role_id = :role_id LIMIT :offset, :limit");
    $statement->bindValue(":role_id", $role_id, PDO::PARAM_INT);
    $statement->bindValue(":offset", ($page - 1) * $page_size, PDO::PARAM_INT);
    $statement->bindValue(":limit", $page_size, PDO::PARAM_INT);
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

function sl_role_get_role_history(PDO $connection, int $role_id): array
{
    $statement = $connection->prepare("SELECT rh.id, rh.status_id, rs.name, rh.created FROM role_history rh LEFT JOIN role_statuses rs ON (rh.status_id = rs.id) WHERE role_id = :role_id ORDER BY rh.created DESC");
    $statement->bindValue(":role_id", $role_id, PDO::PARAM_INT);
    $statement->execute();

    return sl_template_escape_array_of_arrays($statement->fetchAll(PDO::FETCH_ASSOC));
}

function sl_role_change_role_status(PDO $connection, int $role_id, int $status_id)
{
    $statement = $connection->prepare("INSERT INTO role_history (role_id, status_id, created) VALUES (:role_id, :status_id, NOW())");
    $statement->bindValue(":role_id", $role_id, PDO::PARAM_INT);
    $statement->bindValue(":status_id", $status_id, PDO::PARAM_INT);
    $statement->execute();
}

sl_request_methods_assert(["GET", "POST"]);

$role = [
    "id" => 0,
    "name" => "",
    "description" => ""
];
$role_actions = [];
$other_actions = [];
$role_history = [];
$errors = [
    "name" => null,
    "description" => null
];

$connection = sl_database_get_connection();
$statuses = sl_template_escape_array_of_arrays(sl_database_get_role_statuses($connection));

$role_id = sl_request_query_get_integer("id", 0, PHP_INT_MAX);
$page = sl_request_query_get_integer("page", 1, PHP_INT_MAX, 1);
$page_size = sl_request_query_get_integer("size", 10, 100, 10);
$tab_number = sl_request_query_get_integer("tab", 0, 1, 0);

$total_pages = 0;

if (sl_request_is_method("GET") && $role_id > 0) {
    sl_auth_assert_authorized_any(["ReadRole", "UpdateRole"]);

    $role = sl_role_get_role_by_id($connection, $role_id);
    if (empty($role)) {
        sl_request_terminate(404);
    }

    if ($tab_number === 0) {
        $row_count = sl_role_get_role_actions_count($connection, $role_id, $page, $page_size);

        $total_pages = intval(ceil($row_count / $page_size));
        if ($total_pages > 0 && $page > $total_pages) {
            sl_request_terminate(400);
        }

        $role_actions = sl_role_get_role_actions($connection, $role_id, $page, $page_size);
        $other_actions = sl_role_get_other_actions($connection, $role_id);
    } elseif ($tab_number === 1) {
        $role_history = sl_role_get_role_history($connection, $role_id);
    }
} elseif (sl_request_is_method("GET") && $role_id == 0) {
    sl_auth_assert_authorized("CreateRole");
}

if (sl_request_is_method("POST") && sl_request_post_string_equals("action", "add_update_role")) {
    sl_auth_assert_authorized_any(["CreateRole", "UpdateRole"]);

    $role_id = sl_request_post_get_integer("id", 0, PHP_INT_MAX, 0);

    if ($role_id > 0) {
        $existing_role = sl_role_get_role_by_id($connection, $role_id);
        if (empty($existing_role)) {
            sl_request_terminate(404);
        }
    }

    $parameters = sl_request_get_post_parameters([
        "name" => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        "description" => FILTER_SANITIZE_FULL_SPECIAL_CHARS
    ]);

    $role["id"] = $role_id;
    $role["status_id"] = $role_id > 0 ? $existing_role["status_id"] : SL_ROLE_INVALID_STATUS_ID;
    $role["status_history_id"] = $role_id > 0 ? $existing_role["status_history_id"] : SL_ROLE_ACTIVE_STATUS_ID;

    $role["name"] = sl_sanitize_case($parameters["name"], MB_CASE_TITLE_SIMPLE);
    $role["description"] = sl_sanitize_trim($parameters["description"]);

    $errors["name"] = sl_validate_regexp($role["name"], 4, 32, "/^[[:alpha:]]+$/u", "Name", "letters");
    $errors["description"] = sl_validate_regexp($role["description"], 10, 1024, "/^[[:print:]]+$/u", "Description", "printable characters");

    if (!isset($errors["name"]) && !sl_database_is_unique_column($connection, "roles", "name", $role["name"], $role_id)) {
        $errors["name"] = "Role already exists";
    }

    if (!sl_validate_has_errors($errors)) {
        $connection->beginTransaction();

        if ($role_id > 0) {
            sl_auth_assert_authorized("UpdateRole");
            $statement = $connection->prepare(
                "UPDATE roles SET name = :name, description = :description WHERE id = :id"
            );
            $statement->bindValue(":id", $role_id, PDO::PARAM_INT);
        } else {
            sl_auth_assert_authorized("CreateRole");
            $statement = $connection->prepare(
                "INSERT INTO roles (name, description) VALUES (:name, :description)"
            );
        }

        $statement->bindValue(":name", $role["name"], PDO::PARAM_STR);
        $statement->bindValue(":description", $role["description"], PDO::PARAM_STR);
        $statement->execute();

        if ($role_id === 0) {
            $role_id = intval($connection->lastInsertId());

            sl_role_change_role_status($connection, $role_id, SL_ROLE_ACTIVE_STATUS_ID);
        }

        $connection->commit();

        sl_session_set_flash_message($role_id > 0 ? "Role updated successfully" : "Role added successfully");
        sl_request_redirect("/roles");
    } else {
        if ($tab_number === 0) {
            $row_count = sl_role_get_role_actions_count($connection, $role_id, $page, $page_size);

            $total_pages = intval(ceil($row_count / $page_size));
            if ($total_pages > 0 && $page > $total_pages) {
                sl_request_terminate(400);
            }

            $role_actions = sl_role_get_role_actions($connection, $role_id, $page, $page_size);
            $other_actions = sl_role_get_other_actions($connection, $role_id);
        } elseif ($tab_number === 1) {
            $role_history = sl_role_get_role_history($connection, $role_id);
        }
    }
}

if (sl_request_is_method("POST") && sl_request_post_string_equals("action", "add_action")) {
    sl_auth_assert_authorized("UpdateRole");

    $action_id = sl_request_post_get_integer("action_id", 0, PHP_INT_MAX, 0);

    $statement = $connection->prepare("INSERT INTO roles_actions VALUES (:role_id, :action_id)");
    $statement->bindValue(":role_id", $role_id, PDO::PARAM_INT);
    $statement->bindValue(":action_id", $action_id, PDO::PARAM_INT);
    $statement->execute();

    sl_session_set_flash_message("Action added to the role successfully");
    sl_request_redirect("/role/${role_id}");
}

if (sl_request_is_method("POST") && sl_request_post_string_equals("action", "delete_action")) {

    $action_id = sl_request_post_get_integer("action_id", 0, PHP_INT_MAX, 0);

    $statement = $connection->prepare("DELETE FROM roles_actions WHERE role_id = :role_id AND action_id = :action_id");
    $statement->bindValue(":role_id", $role_id, PDO::PARAM_INT);
    $statement->bindValue(":action_id", $action_id, PDO::PARAM_INT);
    $statement->execute();

    sl_session_set_flash_message("Action deleted from the role successfully");
    sl_request_redirect("/role/${role_id}");
}

sl_template_render_header();
sl_template_render_sidebar();
sl_render_role($tab_number, $role, $role_actions, $other_actions, $role_history, $statuses, $errors, "/role/{$role_id}", $page, $page_size, $total_pages);
sl_template_render_footer();
