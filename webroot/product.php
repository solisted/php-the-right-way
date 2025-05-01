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

function sl_render_product(array $product, array $categories, array $errors): void
{
    require("../templates/product.php");
}


sl_request_methods_assert(["GET", "POST"]);

$product = [
    "id" => 0,
    "category_id" => 0,
    "name" => ""
];
$errors = [
    "name" => null
];

$connection = sl_database_get_connection();
$categories = sl_template_escape_array_of_arrays(sl_database_get_categories($connection));

$product_id = sl_request_query_get_integer("id", 0, PHP_INT_MAX);

if (sl_request_is_method("GET")) {
    if ($product_id > 0) {
        $statement = $connection->prepare("SELECT id, category_id, name FROM products WHERE id = :id");
        $statement->bindValue(":id", $product_id, PDO::PARAM_INT);
        $statement->execute();

        if ($statement->rowCount() !== 1) {
            sl_request_terminate(404);
        }

        $product = sl_template_escape_array($statement->fetch(PDO::FETCH_ASSOC));
    }
}

if (sl_request_is_method("POST")) {
    if (!sl_request_post_string_equals("action", "delete")) {
        $parameters = sl_request_get_post_parameters([
            "name" => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            "category_id" => FILTER_SANITIZE_NUMBER_INT
        ]);

        $product["id"] = $product_id;
        $product["category_id"] = intval($parameters["category_id"]);

        if ($product_id > 0 && $product["category_id"] > 0) {
            $category = array_find($categories, function (array $value) use ($product) {
                return isset($value["id"]) && intval($value["id"]) === $product["category_id"];
            });
            if ($category === null) {
                sl_request_terminate(400);
            }

            if ($category["rgt"] - $category["lft"] > 1) {
                sl_request_terminate(400);
            }
        }

        $product["name"] = sl_sanitize_trim($parameters["name"]);

        $errors["name"] = sl_validate_regexp($product["name"], 8, 128, "/^[[:print:]]+$/u", "Name", "printable characters");

        if ($product["category_id"] === 0) {
            $errors["category_id"] = "Select product category";
        }

        if (!isset($errors["name"]) && !sl_database_is_unique_column($connection, "products", "name", $product["name"], $product_id)) {
            $errors["name"] = "Product already exists";
        }

        if (!sl_validate_has_errors($errors)) {
            if ($product_id > 0) {
                $statement = $connection->prepare(
                    "UPDATE products SET name = :name, category_id = :category_id WHERE id = :id"
                );
                $statement->bindValue(":id", $product_id, PDO::PARAM_INT);
            } else {
                $statement = $connection->prepare(
                    "INSERT INTO products (name, category_id) VALUES (:name, :category_id)"
                );
            }

            $statement->bindValue(":category_id", $product["category_id"], PDO::PARAM_INT);
            $statement->bindValue(":name", $product["name"], PDO::PARAM_STR);
            $statement->execute();

            sl_request_redirect("/products");
        }
    } else if (sl_request_post_string_equals("action", "delete")) {
        $category_id = sl_request_query_get_integer("category", 0, PHP_INT_MAX, 0);

        $statement = $connection->prepare("DELETE FROM products WHERE id = :id");
        $statement->bindValue(":id", $product_id, PDO::PARAM_INT);
        $statement->execute();

        sl_request_redirect($category_id == 0 ? "/products" : "/products?category={$category_id}");
    } else {
        sl_request_terminate(400);
    }
}

sl_template_render_header();
sl_template_render_sidebar();
sl_render_product($product, $categories, $errors);
sl_template_render_footer();
