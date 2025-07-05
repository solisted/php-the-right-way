<?php
declare(strict_types=1);

require("../config/config.php");
require("../includes/errors.php");
require("../includes/authentication.php");
require("../includes/authorization.php");
require("../includes/database.php");
require("../includes/request.php");
require("../includes/template.php");

function sl_render_orders(array $orders, string $url, int $page, int $size, int $total_pages): void
{
    require("../templates/orders.php");
}

sl_request_method_assert("GET");

sl_auth_assert_authorized("ListOrders");

$page = sl_request_query_get_integer("page", 1, PHP_INT_MAX, 1);
$page_size = sl_request_query_get_integer("size", 10, 100, 15);

$connection = sl_database_get_connection();

$statement = $connection->query("SELECT COUNT(*) FROM orders");
$row_count = $statement->fetchColumn(0);

$total_pages = intval(ceil($row_count / $page_size));
if ($total_pages > 0 && $page > $total_pages) {
    sl_request_terminate(400);
}

$statement = $connection->prepare("SELECT o.id, o.number, SUBSTRING_INDEX(GROUP_CONCAT(os.name ORDER BY oh.created DESC), ',', 1) AS status, SUBSTRING_INDEX(GROUP_CONCAT(oh.created ORDER BY oh.created DESC), ',', 1) AS updated FROM orders o LEFT JOIN order_history oh ON (oh.order_id = o.id) LEFT JOIN order_statuses os ON (oh.status_id = os.id) GROUP BY o.id LIMIT :offset, :limit");
$statement->bindValue(":offset", ($page - 1) * $page_size, PDO::PARAM_INT);
$statement->bindValue(":limit", $page_size, PDO::PARAM_INT);
$statement->execute();

$orders = sl_template_escape_array_of_arrays($statement->fetchAll(PDO::FETCH_ASSOC));

sl_template_render_header();
sl_template_render_sidebar();
sl_render_orders($orders, "/orders", $page, $page_size, $total_pages);
sl_template_render_footer();
