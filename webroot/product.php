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

function sl_render_product(
    int $tab_number,
    array $product,
    array $attribute,
    array $categories,
    array $product_attributes,
    array $other_attributes,
    array $product_images,
    array $errors): void
{
    require("../templates/product.php");
}

function sl_product_get_product_by_id(PDO $connection, int $product_id): array
{
    $statement = $connection->prepare("SELECT id, category_id, name, description FROM products WHERE id = :id");
    $statement->bindValue(":id", $product_id, PDO::PARAM_INT);
    $statement->execute();

    if ($statement->rowCount() !== 1) {
        return [];
    }

    return sl_template_escape_array($statement->fetch(PDO::FETCH_ASSOC));
}

function sl_product_is_product_attribute(PDO $connection, int $product_id, int $attribute_id): bool
{
    $statement = $connection->prepare("SELECT COUNT(*) FROM attributes a, categories_attributes ca, products p WHERE a.id = ca.attribute_id AND p.category_id = ca.category_id AND p.id = :product_id AND a.id = :attribute_id");
    $statement->bindValue(":product_id", $product_id, PDO::PARAM_INT);
    $statement->bindValue(":attribute_id", $attribute_id, PDO::PARAM_INT);
    $statement->execute();

    return $statement->fetchColumn(0) == 1;
}

function sl_product_get_product_attributes(PDO $connection, int $product_id): array
{
    $statement = $connection->prepare("SELECT id, name, value FROM attributes, products_attributes WHERE attribute_id = id AND product_id = :product_id");
    $statement->bindValue(":product_id", $product_id, PDO::PARAM_INT);
    $statement->execute();

    return sl_template_escape_array_of_arrays($statement->fetchAll(PDO::FETCH_ASSOC));
}

function sl_product_get_other_attributes(PDO $connection, int $product_id, int $category_id): array
{
    $statement = $connection->prepare(
        "SELECT a.id, a.name, '' AS value FROM attributes a LEFT JOIN categories_attributes ca ON ca.attribute_id = a.id LEFT JOIN products_attributes pa ON pa.attribute_id = a.id AND pa.product_id = :product_id WHERE ca.category_id = :category_id AND pa.product_id IS NULL;"
    );
    $statement->bindValue(":product_id", $product_id, PDO::PARAM_INT);
    $statement->bindValue(":category_id", $category_id, PDO::PARAM_INT);
    $statement->execute();

    return sl_template_escape_array_of_arrays($statement->fetchAll(PDO::FETCH_ASSOC));
}

function sl_product_get_product_images(PDO $connection, int $product_id): array
{
    $statement = $connection->prepare("SELECT id, filename FROM images, products_images WHERE image_id = id AND product_id = :product_id");
    $statement->bindValue(":product_id", $product_id, PDO::PARAM_INT);
    $statement->execute();

    return sl_template_escape_array_of_arrays($statement->fetchAll(PDO::FETCH_ASSOC));
}

sl_request_methods_assert(["GET", "POST"]);

$product = [
    "id" => 0,
    "category_id" => 0,
    "name" => "",
    "description" => ""
];
$attribute = [
    "id" => 0,
    "value" => ""
];
$product_attributes = [];
$product_images = [];
$other_attributes = [];
$errors = [
    "name" => null,
    "description" => null,
    "value" => null,
    "image" => null
];

$connection = sl_database_get_connection();
$categories = sl_template_escape_array_of_arrays(sl_database_get_categories($connection));

$product_id = sl_request_query_get_integer("id", 0, PHP_INT_MAX);
$attribute_id = sl_request_post_get_integer("attribute_id", 0, PHP_INT_MAX, 0);
$tab_number = sl_request_query_get_integer("tab", 0, 1, 0);

if (sl_request_is_method("GET") && $product_id > 0) {
    $product = sl_product_get_product_by_id($connection, $product_id);
    if (empty($product)) {
        sl_request_terminate(404);
    }

    if ($tab_number === 0) {
        $product_attributes = sl_product_get_product_attributes($connection, $product_id);
        $other_attributes = sl_product_get_other_attributes($connection, $product_id, intval($product["category_id"]));
    } else if ($tab_number === 1) {
        $product_images = sl_product_get_product_images($connection, $product_id);
    }
}

if (sl_request_is_method("POST") && sl_request_post_string_equals("action", "add_update_product")) {
    $parameters = sl_request_get_post_parameters([
        "name" => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        "category_id" => FILTER_SANITIZE_NUMBER_INT,
        "description" => FILTER_SANITIZE_FULL_SPECIAL_CHARS
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
    $product["description"] = sl_sanitize_trim($parameters["description"], preserve_whitespace: true);

    $errors["name"] = sl_validate_regexp($product["name"], 8, 128, "/^[[:print:]]+$/u", "Name", "printable characters");
    $errors["description"] = sl_validate_regexp($product["description"], 10, 4096, "/^[[:print:][:space:]]+$/u", "Description", "printable characters and whitespace");

    if ($product["category_id"] === 0) {
        $errors["category_id"] = "Select product category";
    }

    if (!isset($errors["name"]) && !sl_database_is_unique_column($connection, "products", "name", $product["name"], $product_id)) {
        $errors["name"] = "Product already exists";
    }

    if (!sl_validate_has_errors($errors)) {
        if ($product_id > 0) {
            $statement = $connection->prepare(
                "UPDATE products SET name = :name, category_id = :category_id, description = :description WHERE id = :id"
            );
            $statement->bindValue(":id", $product_id, PDO::PARAM_INT);
        } else {
            $statement = $connection->prepare(
                "INSERT INTO products (name, category_id, description) VALUES (:name, :category_id, :description)"
            );
        }

        $statement->bindValue(":category_id", $product["category_id"], PDO::PARAM_INT);
        $statement->bindValue(":name", $product["name"], PDO::PARAM_STR);
        $statement->bindValue(":description", $product["description"], PDO::PARAM_STR);
        $statement->execute();

        sl_request_redirect("/products");
    } else if ($tab_number === 0) {
        $product_attributes = sl_product_get_product_attributes($connection, $product_id);
        $other_attributes = sl_product_get_other_attributes($connection, $product_id, intval($product["category_id"]));
    }
}

if (sl_request_is_method("POST") && sl_request_post_string_equals("action", "delete_product")) {
    $category_id = sl_request_query_get_integer("category", 0, PHP_INT_MAX, 0);

    $statement = $connection->prepare("DELETE FROM products WHERE id = :id");
    $statement->bindValue(":id", $product_id, PDO::PARAM_INT);
    $statement->execute();

    sl_request_redirect($category_id == 0 ? "/products" : "/products?category={$category_id}");
}

if (sl_request_is_method("POST") && sl_request_post_string_equals("action", "add_attribute")) {
    if (!sl_product_is_product_attribute($connection, $product_id, $attribute_id)) {
        sl_request_terminate(400);
    }

    $parameters = sl_request_get_post_parameters([
        "value" => FILTER_SANITIZE_FULL_SPECIAL_CHARS
    ]);

    $attribute["id"] = $attribute_id;
    $attribute["value"] = sl_sanitize_trim($parameters["value"]);

    $errors["value"] = sl_validate_regexp($attribute["value"], 1, 128, "/^[[:print:]]+$/u", "Value", "printable characters");

    if (!sl_validate_has_errors($errors)) {
        $statement = $connection->prepare("INSERT INTO products_attributes VALUES (:product_id, :attribute_id, :value)");
        $statement->bindValue(":product_id", $product_id, PDO::PARAM_INT);
        $statement->bindValue(":attribute_id", $attribute_id, PDO::PARAM_INT);
        $statement->bindValue(":value", $attribute["value"], PDO::PARAM_STR);
        $statement->execute();

        sl_request_redirect("/product/{$product_id}");
    } else {
        $product_id = sl_request_post_get_integer("id", 0, PHP_INT_MAX);

        $product = sl_product_get_product_by_id($connection, $product_id);
        if (empty($product)) {
            sl_request_terminate(404);
        }

        if ($tab_number === 0) {
            $product_attributes = sl_product_get_product_attributes($connection, $product_id);
            $other_attributes = sl_product_get_other_attributes($connection, $product_id, intval($product["category_id"]));
        } else if ($tab_number === 1) {
            $product_images = sl_product_get_product_images($connection, $product_id);
        }
    }
}

if (sl_request_is_method("POST") && sl_request_post_string_equals("action", "delete_attribute")) {
    $statement = $connection->prepare("DELETE FROM products_attributes WHERE product_id = :product_id AND attribute_id = :attribute_id");
    $statement->bindValue(":product_id", $product_id, PDO::PARAM_INT);
    $statement->bindValue(":attribute_id", $attribute_id, PDO::PARAM_INT);
    $statement->execute();

    sl_request_redirect("/product/{$product_id}");
}

if (sl_request_is_method("POST") && sl_request_post_string_equals("action", "add_image")) {
    if (!isset($_FILES["image"]) || !is_uploaded_file($_FILES["image"]["tmp_name"])) {
        $errors["image"] = "Image file is required";
    } else {
        $source_file_name = $_FILES["image"]["tmp_name"];
    }

    if (!sl_validate_has_errors($errors) && filesize($source_file_name) > 2097152) {
        $errors["image"] = "Image size should not exceed 2Mb";
    }

    if (!sl_validate_has_errors($errors) && !in_array(mime_content_type($source_file_name), ["image/png", "image/jpeg"])) {
        $errors["image"] = "Only PNG and JPEG images supported";
    }

    if (!sl_validate_has_errors($errors)) {
        $image_info = getimagesize($source_file_name);

        if ($image_info[0] > 1024 || $image_info[1] > 1024) {
            $errors["image"] = "Image dimensions should not exceed 1024 by 1024 pixels";
        }
    }

    if (!sl_validate_has_errors($errors)) {
        $product_id = sl_request_post_get_integer("id", 0, PHP_INT_MAX);

        $destination_extension = str_replace("image/", "", $image_info["mime"]);
        $destination_file_name = bin2hex(random_bytes(16));
        $destination_path_name = "/home/ivan/www/images/{$destination_file_name}.{$destination_extension}";

        move_uploaded_file($source_file_name, $destination_path_name);

        $connection->beginTransaction();

        $statement = $connection->prepare("INSERT INTO images (filename) VALUES (:filename)");
        $statement->bindValue(":filename", $destination_path_name, PDO::PARAM_STR);
        $statement->execute();

        $image_id = $connection->lastInsertId();

        $statement = $connection->prepare("INSERT INTO products_images (product_id, image_id) VALUES (:product_id, :image_id)");
        $statement->bindValue(":product_id", $product_id, PDO::PARAM_INT);
        $statement->bindValue(":image_id", $image_id, PDO::PARAM_INT);
        $statement->execute();

        $connection->commit();

        sl_request_redirect("/product/{$product_id}?tab={$tab_number}");
    } else {
        $product_id = sl_request_post_get_integer("id", 0, PHP_INT_MAX);

        $product = sl_product_get_product_by_id($connection, $product_id);
        if (empty($product)) {
            sl_request_terminate(404);
        }

        if ($tab_number === 0) {
            $product_attributes = sl_product_get_product_attributes($connection, $product_id);
            $other_attributes = sl_product_get_other_attributes($connection, $product_id, intval($product["category_id"]));
        } else if ($tab_number === 1) {
            $product_images = sl_product_get_product_images($connection, $product_id);
        }
    }
}

sl_template_render_header();
sl_template_render_sidebar();
sl_render_product($tab_number, $product, $attribute, $categories, $product_attributes, $other_attributes, $product_images, $errors);
sl_template_render_footer();
