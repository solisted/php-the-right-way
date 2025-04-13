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
        $parent_id = $statement->fetchColumn(0);
    }
}

if (sl_request_is_method("GET")) {
    if ($category_id > 0) {
        $statement = $connection->prepare("SELECT id, name FROM categories WHERE id = :id");
        $statement->bindValue(":id", $category_id, PDO::PARAM_INT);
        $statement->execute();

        if ($statement->rowCount() !== 1) {
            sl_request_terminate(404);
        }

        $category = sl_template_escape_array($statement->fetch(PDO::FETCH_ASSOC));
    }
} else {
    $category_id = sl_request_query_get_integer("id", 0, PHP_INT_MAX);

    $parameters = sl_request_get_post_parameters([
        "name" => FILTER_SANITIZE_FULL_SPECIAL_CHARS
    ]);

    $category["id"] = $category_id;
    $category["name"] = sl_sanitize_categoryname($parameters["name"]);
    $errors["name"] = sl_validate_categoryname($category["name"], "Name");

    if (!isset($errors["name"]) && !sl_database_is_unique_categoryname($connection, $category["name"], $category_id)) {
        $errors["name"] = "Category already exists";
    }

    if (!sl_validate_has_errors($errors)) {
        if ($category_id > 0) {
            // Move category within the hierarchy if submitted parent id is different from actual parent id
            $statement = $connection->prepare(
                "UPDATE categories SET name = :name WHERE id = :id"
            );
            $statement->bindValue(":id", $category_id, PDO::PARAM_INT);
        } else {
            // Create category
        }

        $statement->bindValue(":name", $category["name"], PDO::PARAM_STR);
        $statement->execute();

        sl_request_redirect("/categories");
    }
}

sl_template_render_header();
sl_template_render_sidebar();
sl_render_category($category, $categories, $parent_id, $errors);
sl_template_render_footer();
