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

function sl_render_order_add(
    string $search_term,
    array $found_customers,
    array $errors): void
{
    require("../templates/order_add.php");
}

sl_request_methods_assert(["GET", "POST"]);
sl_auth_assert_authorized("CreateOrder");

$search_term = "";
$found_customers = [
];
$errors = [
    "search_term" => NULL,
];

if (sl_request_is_method("POST") && sl_request_post_string_equals("action", "add_order")) {
    $connection = sl_database_get_connection();

    $customer_id = sl_request_post_get_integer("customer_id", 0, PHP_INT_MAX);

    $statement = $connection->prepare("SELECT COUNT(*) FROM customers WHERE id = :customer_id");
    $statement->bindValue(":customer_id", $customer_id, PDO::PARAM_INT);
    $statement->execute();

    if (intval($statement->fetchColumn(0)) !== 1) {
        sl_request_terminate(400);
    }

    $order_number = "";
    for ($n = 0; $n < 16; $n ++) {
        $order_number .= ord(random_bytes(1)) % 10;
    }

    $connection->beginTransaction();

    $statement = $connection->prepare("INSERT INTO orders (customer_id, number) values (:customer_id, :number)");
    $statement->bindValue(":customer_id", $customer_id, PDO::PARAM_INT);
    $statement->bindValue(":number", $order_number, PDO::PARAM_STR);
    $statement->execute();

    $order_id = $connection->lastInsertId();

    $statement = $connection->prepare("INSERT INTO order_history (order_id, status_id, created) VALUES (:order_id, 1, NOW())");
    $statement->bindValue(":order_id", $order_id, PDO::PARAM_INT);
    $statement->execute();

    $connection->commit();

    sl_session_set_flash_message("Order created successfully");
    sl_request_redirect("/order/{$order_id}");
}

if (sl_request_is_method("POST") && sl_request_post_string_equals("action", "search_customer")) {
    $connection = sl_database_get_connection();

    $parameters = sl_request_get_post_parameters([
        "search_term" => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    ]);

    $search_term = sl_sanitize_trim($parameters["search_term"]);

    $errors["search_term"] = sl_validate_regexp($search_term, 2, 128, "/^[[:print:]]+$/", "Search term", "printable characters");

    if (!sl_validate_has_errors($errors)) {
        $found_customers = sl_template_escape_array_of_arrays(sl_database_search_customers($connection, $search_term));
    }
}

sl_template_render_header();
sl_template_render_sidebar();
sl_render_order_add($search_term, $found_customers, $errors);
sl_template_render_footer();
