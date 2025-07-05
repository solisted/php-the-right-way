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

function sl_render_order(int $tab_number, array $order, array $order_history, array $statuses, array $errors): void
{
    require("../templates/order.php");
}

function sl_order_get_order_by_id(PDO $connection, int $order_id): array
{
    $statement = $connection->prepare("SELECT o.id, o.number, SUBSTRING_INDEX(GROUP_CONCAT(os.name ORDER BY oh.created DESC), ',', 1) AS status, SUBSTRING_INDEX(GROUP_CONCAT(oh.created ORDER BY oh.created DESC), ',', 1) AS updated FROM orders o LEFT JOIN order_history oh ON (oh.order_id = o.id) LEFT JOIN order_statuses os ON (oh.status_id = os.id) WHERE o.id = :id GROUP BY o.id");
    $statement->bindValue(":id", $order_id, PDO::PARAM_INT);
    $statement->execute();

    if ($statement->rowCount() !== 1) {
        return [];
    }

    return sl_template_escape_array($statement->fetch(PDO::FETCH_ASSOC));
}

sl_request_methods_assert(["GET", "POST"]);

$order = [
    "id" => 0,
    "number" => "",
    "status" => 0,
    "updated" => ""
];
$order_history = [];
$errors = [
    "status" => null
];

$connection = sl_database_get_connection();
$statuses = sl_template_escape_array_of_arrays(sl_database_get_statuses($connection));

$order_id = sl_request_query_get_integer("id", 0, PHP_INT_MAX);
$tab_number = sl_request_query_get_integer("tab", 0, 2, 0);

if (sl_request_is_method("GET") && $order_id > 0) {
    sl_auth_assert_authorized("ReadOrder");

    $order = sl_order_get_order_by_id($connection, $order_id);
    if (empty($order)) {
        sl_request_terminate(404);
    }
} else if (sl_request_is_method("GET") && $order_id === 0) {
    sl_auth_assert_authorized("CreateOrder");
}

if (sl_request_is_method("POST") && sl_request_post_string_equals("action", "update_order")) {
    sl_auth_assert_authorized("UpdateOrder");
}

sl_template_render_header();
sl_template_render_sidebar();
sl_render_order($tab_number, $order, $order_history, $statuses, $errors);
sl_template_render_footer();
