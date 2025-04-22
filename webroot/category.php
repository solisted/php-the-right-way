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

function sl_render_category(array $category, array $categories, int $parent_id, array $errors): void
{
    require("../templates/category.php");
}

function sl_categories_find_category_by_id(int $category_id, array $categories): ?array
{
    return array_find($categories, function (array $value) use ($category_id) {
        return isset($value["id"]) && intval($value["id"]) === $category_id;
    });
}

sl_request_methods_assert(["GET", "POST"]);

$category = [
    "id" => 0,
    "name" => ""
];
$errors = [
    "name" => null
];
$parent_id = 0;

$connection = sl_database_get_connection();

$category_id = sl_request_query_get_integer("id", 0, PHP_INT_MAX);

$statement = $connection->query("SELECT node.id, node.name, node.lft, node.rgt, (COUNT(parent.id) - 1) AS depth FROM categories AS node, categories AS parent WHERE node.lft BETWEEN parent.lft AND parent.rgt GROUP BY node.id ORDER BY node.lft");
$statement->execute();

$categories = sl_template_escape_array_of_arrays($statement->fetchAll(PDO::FETCH_ASSOC));

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
    }
} else {
    $category_id = sl_request_post_get_integer("id", 0, PHP_INT_MAX);

    if (!isset($_POST["action"]) || $_POST["action"] !== "delete") {
        $parent_id = sl_request_post_get_integer("parent_id", 0, PHP_INT_MAX, 0);

        $parameters = sl_request_get_post_parameters([
            "name" => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        ]);

        $category["id"] = $category_id;
        $category["name"] = sl_sanitize_categoryname($parameters["name"]);
        $errors["name"] = sl_validate_categoryname($category["name"], "Name");

        if (!isset($errors["name"]) && !sl_database_is_unique_categoryname($connection, $category["name"], $category_id)) {
            $errors["name"] = "Category already exists";
        }

        if ($parent_id === 0 && $category_id === 0) {
            $errors["parent"] = "Select parent category";
        }

        if (!sl_validate_has_errors($errors)) {
            if ($category_id > 0) {
                if ($category_parent_id === $parent_id) {
                    $statement = $connection->prepare(
                        "UPDATE categories SET name = :name WHERE id = :id"
                    );
                    $statement->bindValue(":id", $category_id, PDO::PARAM_INT);
                    $statement->bindValue(":name", $category["name"], PDO::PARAM_STR);
                    $statement->execute();
                } else {
                    // Move category from one parent to another on the same level
                }

            } else {
                $parent = array_find($categories, function (array $value) use ($parent_id) {
                    return isset($value["id"]) && intval($value["id"]) === $parent_id;
                });

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
        }
    } else {
        $category = sl_categories_find_category_by_id($category_id, $categories);
        if ($category === null) {
            sl_request_terminate(404);
        }


        if ($category["depth"] == 0 || $category["rgt"] - $category["lft"] > 1) {
            sl_request_terminate(400);
        }

        $connection->beginTransaction();

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
    }
}

sl_template_render_header();
sl_template_render_sidebar();
sl_render_category($category, $categories, $parent_id, $errors);
sl_template_render_footer();
