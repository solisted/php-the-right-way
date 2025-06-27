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

function sl_render_attribute(array $attribute, array $errors): void
{
    require("../templates/attribute.php");
}

sl_request_methods_assert(["GET", "POST"]);

$attribute = [
    "id" => 0,
    "name" => ""
];
$errors = [
    "name" => null
];

$connection = sl_database_get_connection();

$attribute_id = sl_request_query_get_integer("id", 0, PHP_INT_MAX);

if (sl_request_is_method("GET")) {
    if ($attribute_id > 0) {
        sl_auth_assert_authorized_any(["ReadAttribute", "UpdateAttribute"]);

        $statement = $connection->prepare("SELECT id, name FROM attributes WHERE id = :id");
        $statement->bindValue(":id", $attribute_id, PDO::PARAM_INT);
        $statement->execute();

        if ($statement->rowCount() !== 1) {
            sl_request_terminate(404);
        }

        $attribute = sl_template_escape_array($statement->fetch(PDO::FETCH_ASSOC));
    } else {
        sl_auth_assert_authorized("CreateAttribute");
    }
}

if (sl_request_is_method("POST")) {
    if (!sl_request_post_string_equals("action", "delete")) {
        sl_auth_assert_authorized_any(["CreateAttribute", "UpdateAttribute"]);

        $parameters = sl_request_get_post_parameters([
            "name" => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        ]);

        $attribute["id"] = $attribute_id;

        $attribute["name"] = sl_sanitize_trim($parameters["name"]);

        $errors["name"] = sl_validate_regexp($attribute["name"], 4, 32, "/^[[:alpha:][:blank:]]+$/u", "Name", "letters and space characters");

        if (!isset($errors["name"]) && !sl_database_is_unique_column($connection, "attributes", "name", $attribute["name"], $attribute_id)) {
            $errors["name"] = "Attribute already exists";
        }

        if (!sl_validate_has_errors($errors)) {
            if ($attribute_id > 0) {
                sl_auth_assert_authorized("UpdateAttribute");

                $statement = $connection->prepare(
                    "UPDATE attributes SET name = :name WHERE id = :id"
                );
                $statement->bindValue(":id", $attribute_id, PDO::PARAM_INT);
                sl_session_set_flash_message("Attribute updated successfully");
            } else {
                sl_auth_assert_authorized("CreateAttribute");

                $statement = $connection->prepare(
                    "INSERT INTO attributes (name) VALUES (:name)"
                );
                sl_session_set_flash_message("Attribute added successfully");
            }

            $statement->bindValue(":name", $attribute["name"], PDO::PARAM_STR);
            $statement->execute();

            sl_request_redirect("/attributes");
        }
    } else if (sl_request_post_string_equals("action", "delete")) {
        sl_auth_assert_authorized("DeleteAttribute");

        $statement = $connection->prepare("DELETE FROM attributes WHERE id = :id");
        $statement->bindValue(":id", $attribute_id, PDO::PARAM_INT);
        $statement->execute();

        sl_session_set_flash_message("Attribute deleted successfully");
        sl_request_redirect("/attributes");
    } else {
        sl_request_terminate(400);
    }
}

sl_template_render_header();
sl_template_render_sidebar();
sl_render_attribute($attribute, $errors);
sl_template_render_footer();
