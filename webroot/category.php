<?php
declare(strict_types=1);

require("../includes/errors.php");
require("../includes/authentication.php");
require("../includes/authorization.php");
require("../includes/database.php");
require("../includes/request.php");
require("../includes/template.php");
require("../includes/validate.php");
require("../includes/sanitize.php");

function sl_render_category(array $category, array $categories, array $category_attributes, array $other_attributes, int $parent_id, array $errors): void
{
    require("../templates/category.php");
}

function sl_categories_find_category_by_id(int $category_id, array $categories): ?array
{
    return array_find($categories, function (array $value) use ($category_id) {
        return isset($value["id"]) && intval($value["id"]) === $category_id;
    });
}

function sl_category_get_category_attributes(PDO $connection, int $category_id): array
{
    $statement = $connection->prepare("SELECT id, name FROM attributes, categories_attributes WHERE attribute_id = id AND category_id = :category_id");
    $statement->bindValue(":category_id", $category_id, PDO::PARAM_INT);
    $statement->execute();

    return sl_template_escape_array_of_arrays($statement->fetchAll(PDO::FETCH_ASSOC));
}

function sl_category_get_other_attributes(PDO $connection, int $category_id): array
{
    $statement = $connection->prepare(
        "SELECT id, name FROM attributes LEFT JOIN categories_attributes ON attribute_id = id AND category_id = :category_id WHERE category_id IS NULL"
    );
    $statement->bindValue(":category_id", $category_id, PDO::PARAM_INT);
    $statement->execute();

    return sl_template_escape_array_of_arrays($statement->fetchAll(PDO::FETCH_ASSOC));
}

sl_request_methods_assert(["GET", "POST"]);

$category = [
    "id" => 0,
    "name" => ""
];
$category_attributes = [];
$other_attributes = [];
$errors = [
    "name" => null
];
$parent_id = 0;

$connection = sl_database_get_connection();
$categories = sl_template_escape_array_of_arrays(sl_database_get_categories($connection));

$category_id = sl_request_query_get_integer("id", 0, PHP_INT_MAX);

if ($category_id > 0) {
    $statement = $connection->prepare("SELECT parent.id FROM categories AS node, categories AS parent WHERE node.lft BETWEEN parent.lft AND parent.rgt AND node.id = :id ORDER BY parent.lft DESC LIMIT 1, 1");
    $statement->bindValue(":id", $category_id, PDO::PARAM_INT);
    $statement->execute();

    if ($statement->rowCount() == 1) {
        $category_parent_id = intval($statement->fetchColumn(0));
    } else {
        $category_parent_id = 0;
    }
}

if (sl_request_is_method("GET")) {
    if ($category_id > 0) {
        $category = sl_categories_find_category_by_id($category_id, $categories);
        if ($category === null) {
            sl_request_terminate(404);
        }

        $parent_id = $category_parent_id;

        $category_attributes = sl_category_get_category_attributes($connection, $category_id);
        $other_attributes = sl_category_get_other_attributes($connection, $category_id);
    }
} else {
    $attribute_id = sl_request_post_get_integer("attribute_id", 0, PHP_INT_MAX, 0);

    if ($attribute_id === 0) {
        if (!sl_request_post_string_equals("action", "delete")) {
            $parent_id = sl_request_post_get_integer("parent_id", 0, PHP_INT_MAX, 0);

            $parameters = sl_request_get_post_parameters([
                "name" => FILTER_SANITIZE_FULL_SPECIAL_CHARS
            ]);

            $category["id"] = $category_id;
            $category["name"] = sl_sanitize_case($parameters["name"], MB_CASE_TITLE_SIMPLE);
            $errors["name"] = sl_validate_regexp($category["name"], 4, 64, "/^[[:alpha:][:space:]]+$/u", "Name", "letters and space character");

            if (!isset($errors["name"]) && !sl_database_is_unique_column($connection, "categories", "name", $category["name"], $category_id)) {
                $errors["name"] = "Category already exists";
            }

            if ($parent_id === 0 && $category_id === 0) {
                $errors["parent"] = "Select parent category";
            }

            if (!sl_validate_has_errors($errors)) {
                if ($category_id > 0) {
                    if ($category_parent_id === $parent_id) {
                        $statement = $connection->prepare("UPDATE categories SET name = :name WHERE id = :id");
                        $statement->bindValue(":id", $category_id, PDO::PARAM_INT);
                        $statement->bindValue(":name", $category["name"], PDO::PARAM_STR);
                        $statement->execute();
                    } else {
                        // Move category from one parent to another on the same level
                    }

                } else {
                    $parent = sl_categories_find_category_by_id($parent_id, $categories);

                    if ($parent === null) {
                        sl_request_terminate(400);
                    }

                    $connection->beginTransaction();

                    if ($parent["rgt"] == $parent["lft"] + 1) {
                        $statement = $connection->prepare("UPDATE categories SET lft = lft + 2 WHERE lft > :left");
                        $statement->bindValue(":left", $parent["lft"], PDO::PARAM_INT);
                        $statement->execute();

                        $statement = $connection->prepare("UPDATE categories SET rgt = rgt + 2 WHERE rgt > :left");
                        $statement->bindValue(":left", $parent["lft"], PDO::PARAM_INT);
                        $statement->execute();

                        $left = $parent["lft"] + 1;
                        $right = $parent["lft"] + 2;
                    } else {
                        $statement = $connection->prepare("UPDATE categories SET lft = lft + 2 WHERE lft > :right");
                        $statement->bindValue(":right", $parent["rgt"], PDO::PARAM_INT);
                        $statement->execute();

                        $statement = $connection->prepare("UPDATE categories SET rgt = rgt + 2 WHERE rgt >= :right");
                        $statement->bindValue(":right", $parent["rgt"], PDO::PARAM_INT);
                        $statement->execute();

                        $left = $parent["rgt"];
                        $right = $parent["rgt"] + 1;
                    }

                    $statement = $connection->prepare("INSERT INTO categories (name, lft, rgt) VALUES (:name, :left, :right)");
                    $statement->bindValue(":name", $category["name"], PDO::PARAM_STR);
                    $statement->bindValue(":left", $left, PDO::PARAM_INT);
                    $statement->bindValue(":right", $right, PDO::PARAM_INT);
                    $statement->execute();

                    $connection->commit();
                }

                sl_request_redirect("/categories");
            } else {
                $category_attributes = sl_category_get_category_attributes($connection, $category_id);
                $other_attributes = sl_category_get_other_attributes($connection, $category_id);
            }
        } else if (sl_request_post_string_equals("action", "delete")) {
            $category = sl_categories_find_category_by_id($category_id, $categories);
            if ($category === null) {
                sl_request_terminate(404);
            }


            if ($category["depth"] == 0 || $category["rgt"] - $category["lft"] > 1) {
                sl_request_terminate(400);
            }

            $connection->beginTransaction();

            $statement = $connection->prepare("DELETE FROM categories_attributes WHERE category_id = :id");
            $statement->bindValue(":id", $category_id, PDO::PARAM_INT);
            $statement->execute();

            $statement = $connection->prepare("DELETE FROM categories WHERE id = :id");
            $statement->bindValue(":id", $category_id, PDO::PARAM_INT);
            $statement->execute();

            $statement = $connection->prepare("UPDATE categories SET lft = lft - 2 WHERE lft > :right");
            $statement->bindValue(":right", $category["rgt"], PDO::PARAM_INT);
            $statement->execute();

            $statement = $connection->prepare("UPDATE categories SET rgt = rgt - 2 WHERE rgt > :right");
            $statement->bindValue(":right", $category["rgt"], PDO::PARAM_INT);
            $statement->execute();

            $connection->commit();

            sl_request_redirect("/categories");
        } else {
            sl_request_terminate(400);
        }
    } else if ($attribute_id > 0) {
        $category = sl_categories_find_category_by_id($category_id, $categories);
        if ($category === null || $category["rgt"] != $category["lft"] + 1) {
            sl_request_terminate(400);
        }

        if (sl_request_post_string_equals("action", "add_attribute")) {
            $statement = $connection->prepare("INSERT INTO categories_attributes VALUES (:category_id, :attribute_id)");
            $statement->bindValue(":category_id", $category_id, PDO::PARAM_INT);
            $statement->bindValue(":attribute_id", $attribute_id, PDO::PARAM_INT);
            $statement->execute();

            sl_request_redirect("/category/${category_id}");
        } else if (sl_request_post_string_equals("action", "delete_attribute")) {
            $statement = $connection->prepare("DELETE FROM categories_attributes WHERE category_id = :category_id AND attribute_id = :attribute_id");
            $statement->bindValue(":category_id", $category_id, PDO::PARAM_INT);
            $statement->bindValue(":attribute_id", $attribute_id, PDO::PARAM_INT);
            $statement->execute();

            sl_request_redirect("/category/${category_id}");
        } else {
            sl_request_terminate(400);
        }
    } else {
        sl_request_terminate(400);
    }
}

sl_template_render_header();
sl_template_render_sidebar();
sl_render_category($category, $categories, $category_attributes, $other_attributes, $parent_id, $errors);
sl_template_render_footer();
