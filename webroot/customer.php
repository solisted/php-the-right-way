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

function sl_render_customer(array $customer, array $errors): void
{
    require("../templates/customer.php");
}

sl_request_methods_assert(["GET", "POST"]);

$customer = [
    "id" => 0,
    "first_name" => "",
    "last_name" => "",
    "email" => ""
];
$errors = [
    "first_name" => null,
    "last_name" => null,
    "email" => null
];

$connection = sl_database_get_connection();

$customer_id = sl_request_query_get_integer("id", 0, PHP_INT_MAX);

if (sl_request_is_method("GET")) {
    if ($customer_id > 0) {
        sl_auth_assert_authorized_any(["ReadCustomer", "UpdateCustomer"]);

        $statement = $connection->prepare("SELECT id, first_name, last_name, email FROM customers WHERE id = :id");
        $statement->bindValue(":id", $customer_id, PDO::PARAM_INT);
        $statement->execute();

        if ($statement->rowCount() !== 1) {
            sl_request_terminate(404);
        }

        $customer = sl_template_escape_array($statement->fetch(PDO::FETCH_ASSOC));
    } else {
        sl_auth_assert_authorized("CreateCustomer");
    }
}

if (sl_request_is_method("POST")) {
    sl_auth_assert_authorized_any(["CreateCustomer", "UpdateCustomer"]);

    $parameters = sl_request_get_post_parameters([
        "first_name" => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        "last_name" => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        "email" => FILTER_SANITIZE_FULL_SPECIAL_CHARS
    ]);

    $customer["id"] = $customer_id;

    $customer["first_name"] = sl_sanitize_case($parameters["first_name"], MB_CASE_TITLE_SIMPLE);
    $customer["last_name"] = sl_sanitize_case($parameters["last_name"], MB_CASE_TITLE_SIMPLE);
    $customer["email"] = sl_sanitize_case($parameters["email"], MB_CASE_LOWER_SIMPLE);

    $errors["first_name"] = sl_validate_regexp($customer["first_name"], 2, 32, "/^[[:alpha:]]+$/u", "First name", "letters");
    $errors["last_name"] = sl_validate_regexp($customer["last_name"], 2, 32, "/^[[:alpha:]]+$/u", "Last name", "letters");
    $errors["email"] = sl_validate_email($customer["email"], "Email");

    if (!isset($errors["email"]) && !sl_database_is_unique_column($connection, "customers", "email", $customer["email"], $customer_id)) {
        $errors["email"] = "Email already exists";
    }

    if (!sl_validate_has_errors($errors)) {
        if ($customer_id > 0) {
            sl_auth_assert_authorized("UpdateCustomer");

            $statement = $connection->prepare(
                "UPDATE customers SET first_name = :first_name, last_name = :last_name, email = :email WHERE id = :id"
            );

            $statement->bindValue(":id", $customer_id, PDO::PARAM_INT);
        } else {
            sl_auth_assert_authorized("CreateCustomer");

            $statement = $connection->prepare(
                "INSERT INTO customers (first_name, last_name, email) VALUES (:first_name, :last_name, :email)"
            );
        }

        $statement->bindValue(":first_name", $customer["first_name"], PDO::PARAM_STR);
        $statement->bindValue(":last_name", $customer["last_name"], PDO::PARAM_STR);
        $statement->bindValue(":email", $customer["email"], PDO::PARAM_STR);
        $statement->execute();

        sl_session_set_flash_message($customer_id > 0 ? "Customer updated successfully" : "Customer added successfully");
        sl_request_redirect("/customers");
    }
}

sl_template_render_header();
sl_template_render_sidebar();
sl_render_customer($customer, $errors);
sl_template_render_footer();
