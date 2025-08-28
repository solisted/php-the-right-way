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

function sl_render_order(
    int $tab_number,
    array $order,
    array $order_item,
    array $order_items,
    array $found_products,
    array $order_history,
    array $statuses,
    array $errors): void
{
    require("../templates/order.php");
}

function sl_order_get_order_by_id(PDO $connection, int $order_id): array
{
    $statement = $connection->prepare("SELECT o.id, o.number, c.first_name, c.last_name, SUBSTRING_INDEX(GROUP_CONCAT(oh.id ORDER BY oh.created DESC), ',', 1) AS status_history_id, SUBSTRING_INDEX(GROUP_CONCAT(oh.status_id ORDER BY oh.created DESC), ',', 1) AS status_id, SUBSTRING_INDEX(GROUP_CONCAT(os.name ORDER BY oh.created DESC), ',', 1) AS status, SUBSTRING_INDEX(GROUP_CONCAT(oh.created ORDER BY oh.created DESC), ',', 1) AS updated FROM orders o LEFT JOIN order_history oh ON (oh.order_id = o.id) LEFT JOIN order_statuses os ON (oh.status_id = os.id) LEFT JOIN customers c ON (o.customer_id = c.id) WHERE o.id = :id GROUP BY o.id");
    $statement->bindValue(":id", $order_id, PDO::PARAM_INT);
    $statement->execute();

    if ($statement->rowCount() !== 1) {
        return [];
    }

    return sl_template_escape_array($statement->fetch(PDO::FETCH_ASSOC));
}

function sl_order_get_order_items(PDO $connection, int $order_id): array
{
    $statement = $connection->prepare("SELECT oi.id, p.id AS product_id, p.sku, p.name, pp.price, oi.quantity, pp.id AS product_price_id FROM order_items oi LEFT JOIN product_prices pp ON (pp.id = oi.product_price_id) LEFT JOIN products p ON (pp.product_id = p.id) WHERE oi.order_id = :order_id");
    $statement->bindValue(":order_id", $order_id, PDO::PARAM_INT);
    $statement->execute();

    return sl_template_escape_array_of_arrays($statement->fetchAll(PDO::FETCH_ASSOC));
}

function sl_order_get_order_history(PDO $connection, int $order_id): array
{
    $statement = $connection->prepare("SELECT oh.id, oh.status_id, os.name, oh.created FROM order_history oh LEFT JOIN order_statuses os ON (oh.status_id = os.id) WHERE order_id = :order_id ORDER BY oh.created DESC");
    $statement->bindValue(":order_id", $order_id, PDO::PARAM_INT);
    $statement->execute();

    return sl_template_escape_array_of_arrays($statement->fetchAll(PDO::FETCH_ASSOC));
}

function sl_order_has_product(PDO $connection, int $order_id, int $product_id): bool
{
    $statement = $connection->prepare("SELECT COUNT(*) FROM order_items oi LEFT JOIN product_prices pp ON (oi.product_price_id = pp.id) WHERE oi.order_id = :order_id AND pp.product_id = :product_id");
    $statement->bindValue(":order_id", $order_id, PDO::PARAM_INT);
    $statement->bindValue(":product_id", $product_id, PDO::PARAM_INT);
    $statement->execute();

    return ($statement->fetchColumn(0) > 0);
}

function sl_order_find_order_item_index_by_id(array $order_items, int $item_id): ?int
{
    foreach ($order_items as $index => $value) {
        if (intval($value["id"]) === $item_id) {
            return $index;
        }
    }

    return null;
}

sl_request_methods_assert(["GET", "POST"]);

$order = [
    "id" => 0,
    "number" => "",
    "first_name" => "",
    "last_name" => "",
    "number" => "",
    "status_id" => 0,
    "status" => "",
    "updated" => ""
];
$order_item = [
    "sku" => "",
    "quantity" => 1
];
$order_items = [];
$found_products = [];
$order_history = [];
$errors = [
    "status_id" => null,
    "sku" => null,
    "quantity" => null
];

$connection = sl_database_get_connection();
$statuses = sl_template_escape_array_of_arrays(sl_database_get_order_statuses($connection));

$order_id = sl_request_query_get_integer("id", 0, PHP_INT_MAX);
$tab_number = sl_request_query_get_integer("tab", 0, 2, 0);

if (sl_request_is_method("GET") && $order_id > 0) {
    sl_auth_assert_authorized("ReadOrder");

    $order = sl_order_get_order_by_id($connection, $order_id);
    if (empty($order)) {
        sl_request_terminate(404);
    }

    if ($tab_number === 0) {
        $order_items = sl_order_get_order_items($connection, $order_id);
    } else if ($tab_number === 1) {
        $order_history = sl_order_get_order_history($connection, $order_id);
    }
} else if (sl_request_is_method("GET") && $order_id === 0) {
    sl_auth_assert_authorized("CreateOrder");
}

if (sl_request_is_method("POST") && sl_request_post_string_equals("action", "update_order")) {
    sl_auth_assert_authorized("UpdateOrder");

    $order_id = sl_request_post_get_integer("id", 0, PHP_INT_MAX);

    $order = sl_order_get_order_by_id($connection, $order_id);
    if (empty($order)) {
        sl_request_terminate(404);
    }

    $parameters = sl_request_get_post_parameters([
        "status_id" => FILTER_SANITIZE_NUMBER_INT,
    ]);

    $new_status_id = intval($parameters["status_id"]);
    $new_status = array_find($statuses, function (array $value) use ($new_status_id) {
        return isset($value["id"]) && intval($value["id"]) === $new_status_id;
    });

    if ($new_status === null) {
        $errors["status_id"] = "Please, select status";
        $order["status_id"] = 0;
        $order["status"] = "";
    }

    if (!sl_validate_has_errors($errors)) {
        if ($order["status_id"] != $new_status_id) {
            $statement = $connection->prepare("INSERT INTO order_history (order_id, status_id, created) VALUES (:order_id, :status_id, NOW())");
            $statement->bindValue(":order_id", $order_id, PDO::PARAM_INT);
            $statement->bindValue(":status_id", $new_status_id, PDO::PARAM_INT);
            $statement->execute();
        }

        sl_session_set_flash_message("Order updated successfully");
        sl_request_redirect("/orders");
    } else if ($tab_number === 0) {
        $order_items = sl_order_get_order_items($connection, $order_id);
    } else if ($tab_number === 1) {
        $order_history = sl_order_get_order_history($connection, $order_id);
    }
}

if (sl_request_is_method("POST") && sl_request_post_string_equals("action", "add_item")) {
    sl_auth_assert_authorized("UpdateOrder");

    $order_id = sl_request_post_get_integer("id", 0, PHP_INT_MAX);

    $order = sl_order_get_order_by_id($connection, $order_id);
    if (empty($order)) {
        sl_request_terminate(404);
    }

    $parameters = sl_request_get_post_parameters([
        "sku" => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        "quantity" => FILTER_SANITIZE_NUMBER_INT
    ]);

    $order_item["sku"] = sl_sanitize_case($parameters["sku"], MB_CASE_UPPER_SIMPLE);
    $order_item["quantity"] = sl_sanitize_trim($parameters["quantity"]);

    $errors["sku"] = sl_validate_regexp($order_item["sku"], 8, 16, "/^[A-Z0-9]+$/u", "SKU", "alphanumeric characters");
    $errors["quantity"] = sl_validate_regexp($order_item["quantity"], 1, 8, "/^[[:digit:]]+$/u", "Quantity", "integer number");

    if (!isset($errors["quantity"]) && intval($order_item["quantity"]) < 1) {
        $errors["quantity"] = "Specify order item quantity greater than zero";
    }

    if (!isset($errors["sku"])) {
        $product = sl_template_escape_array(sl_database_get_product_by_sku($connection, $order_item["sku"]));
        if (empty($product)) {
            $errors["sku"] = "Product with specified SKU does not exist";
        }
    }

    if (!isset($errors["sku"]) && sl_order_has_product($connection, $order_id, intval($product["id"]))) {
         $errors["sku"] = "Product with specified SKU already exists";
    }

    if (!sl_validate_has_errors($errors)) {
        $statement = $connection->prepare("INSERT INTO order_items (order_id, product_price_id, quantity) VALUES (:order_id, :product_price_id, :quantity)");
        $statement->bindValue(":order_id", $order_id, PDO::PARAM_INT);
        $statement->bindValue(":product_price_id", $product["product_price_id"], PDO::PARAM_INT);
        $statement->bindValue(":quantity", $order_item["quantity"], PDO::PARAM_INT);
        $statement->execute();

        sl_session_set_flash_message("Order item added successfully");
    }

    if ($tab_number === 0) {
        $order_items = sl_order_get_order_items($connection, $order_id);
    } else if ($tab_number === 1) {
        $order_history = sl_order_get_order_history($connection, $order_id);
    }
}

if (sl_request_is_method("POST") && sl_request_post_string_equals("action", "delete_item")) {
    sl_auth_assert_authorized("UpdateOrder");

    $order_id = sl_request_post_get_integer("id", 0, PHP_INT_MAX);
    $item_id = sl_request_post_get_integer("item_id", 0, PHP_INT_MAX);

    $order_items = sl_order_get_order_items($connection, $order_id);

    $item = array_find($order_items, function (array $value) use ($item_id) {
        return isset($value["id"]) && intval($value["id"]) === $item_id;
    });
    if ($item === null) {
        sl_request_terminate(400);
    }

    $order = sl_order_get_order_by_id($connection, $order_id);
    if (empty($order)) {
        sl_request_terminate(404);
    }

    $statement = $connection->prepare("DELETE FROM order_items WHERE id = :item_id");
    $statement->bindValue(":item_id", $item_id, PDO::PARAM_INT);
    $statement->execute();

    sl_session_set_flash_message("Order item deleted successfully");
    sl_request_redirect("/order/{$order_id}");
}

if (sl_request_is_method("POST") && sl_request_post_string_equals_any("action", ["decrease_item", "increase_item"])) {
    sl_auth_assert_authorized("UpdateOrder");

    $order_id = sl_request_post_get_integer("id", 0, PHP_INT_MAX);
    $item_id = sl_request_post_get_integer("item_id", 0, PHP_INT_MAX);

    $order_items = sl_order_get_order_items($connection, $order_id);

    $item_index = sl_order_find_order_item_index_by_id($order_items, $item_id);
    if ($item_index === null) {
        sl_request_terminate(400);
    }
    $item = $order_items[$item_index];

    $order = sl_order_get_order_by_id($connection, $order_id);
    if (empty($order)) {
        sl_request_terminate(404);
    }

    if (sl_request_post_string_equals("action", "decrease_item")) {
        $statement = $connection->prepare("UPDATE order_items SET quantity = quantity - 1 WHERE id = :item_id");
        $statement->bindValue(":item_id", $item_id, PDO::PARAM_INT);
        $statement->execute();

        $order_items[$item_index]["quantity"] -= 1;
    } else {
        $statement = $connection->prepare("UPDATE order_items SET quantity = quantity + 1 WHERE id = :item_id");
        $statement->bindValue(":item_id", $item_id, PDO::PARAM_INT);
        $statement->execute();

        $order_items[$item_index]["quantity"] += 1;
    }
}

if (sl_request_is_method("POST") && sl_request_post_string_equals("action", "search_item")) {
    sl_auth_assert_authorized("UpdateOrder");
    $order_id = sl_request_post_get_integer("id", 0, PHP_INT_MAX);

    $order_items = sl_order_get_order_items($connection, $order_id);
    $order = sl_order_get_order_by_id($connection, $order_id);
    if (empty($order)) {
        sl_request_terminate(404);
    }

    $parameters = sl_request_get_post_parameters([
        "sku" => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        "quantity" => FILTER_SANITIZE_NUMBER_INT
    ]);

    $search_term = sl_sanitize_trim($parameters["sku"]);
    $order_item["quantity"] = sl_sanitize_trim($parameters["quantity"]);

    $errors["sku"] = sl_validate_regexp($search_term, 2, 128, "/^[[:print:]]+$/", "Search term", "printable characters");

    if (!sl_validate_has_errors($errors)) {
        $order_item["sku"] = $search_term;

        $found_products = sl_template_escape_array_of_arrays(sl_database_search_products($connection, $search_term));
    }
}

if (sl_request_is_method("POST") && sl_request_post_string_equals("action", "add_product")) {
    sl_auth_assert_authorized("UpdateOrder");

    $order_id = sl_request_post_get_integer("id", 0, PHP_INT_MAX);

    $order = sl_order_get_order_by_id($connection, $order_id);
    if (empty($order)) {
        sl_request_terminate(404);
    }

    $product_price_id = sl_request_post_get_integer("price_id", 0, PHP_INT_MAX);
    $quantity = sl_request_post_get_integer("quantity", 1, 10);

    $connection->beginTransaction();

    $statement = $connection->prepare("SELECT COUNT(*) FROM product_prices WHERE id = :product_price_id");
    $statement->bindValue(":product_price_id", $product_price_id, PDO::PARAM_INT);
    $statement->execute();

    if (intval($statement->fetchColumn(0)) === 0) {
        sl_request_terminate(400);
    }

    $statement = $connection->prepare("SELECT COUNT(*) FROM order_items WHERE order_id = :order_id AND product_price_id = :product_price_id");
    $statement->bindValue(":order_id", $order_id, PDO::PARAM_INT);
    $statement->bindValue(":product_price_id", $product_price_id, PDO::PARAM_INT);
    $statement->execute();

    if (intval($statement->fetchColumn(0)) !== 0) {
        sl_request_terminate(400);
    }

    $statement = $connection->prepare("INSERT INTO order_items (order_id, product_price_id, quantity) VALUES (:order_id, :product_price_id, :quantity)");
    $statement->bindValue(":order_id", $order_id, PDO::PARAM_INT);
    $statement->bindValue(":product_price_id", $product_price_id, PDO::PARAM_INT);
    $statement->bindValue(":quantity", $quantity, PDO::PARAM_INT);
    $statement->execute();

    $connection->commit();

    sl_session_set_flash_message("Order item added successfully");
    sl_request_redirect("/order/{$order_id}?tab={$tab_number}");
}

sl_template_render_header();
sl_template_render_sidebar();
sl_render_order($tab_number, $order, $order_item, $order_items, $found_products, $order_history, $statuses, $errors);
sl_template_render_footer();
