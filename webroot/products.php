<?php
declare(strict_types=1);

require("../config/config.php");
require("../includes/errors.php");
require("../includes/authentication.php");
require("../includes/authorization.php");
require("../includes/database.php");
require("../includes/request.php");
require("../includes/template.php");

function sl_render_products(array $products, int $category_id, string $url, int $page, int $size, int $total_pages): void
{
    require("../templates/products.php");
}

sl_request_method_assert("GET");

$page = sl_request_query_get_integer("page", 1, PHP_INT_MAX, 1);
$page_size = sl_request_query_get_integer("size", 10, 100, 15);
$category_id = sl_request_query_get_integer("category", 0, PHP_INT_MAX, 0);

$connection = sl_database_get_connection();

if ($category_id == 0) {
    $statement = $connection->query("SELECT COUNT(*) FROM products");
} else {
    $statement = $connection->prepare("SELECT COUNT(*) FROM products p WHERE p.category_id = :category_id");
    $statement->bindValue(":category_id", $category_id, PDO::PARAM_INT);
    $statement->execute();
}

$row_count = $statement->fetchColumn(0);

$total_pages = intval(ceil($row_count / $page_size));
if ($total_pages > 0 && $page > $total_pages) {
    sl_request_terminate(400);
}

if ($category_id == 0) {
    $statement = $connection->prepare("SELECT p.id, p.sku, p.name, c.name AS category, SUBSTRING_INDEX(GROUP_CONCAT(pp.price ORDER BY pp.created DESC), ',', 1) AS price FROM products p LEFT JOIN categories c ON (p.category_id = c.id) LEFT JOIN product_prices pp ON (p.id = pp.product_id) GROUP BY p.id LIMIT :offset, :limit");
} else {
    $statement = $connection->prepare("SELECT p.id, p.sku, p.name, c.name AS category, SUBSTRING_INDEX(GROUP_CONCAT(pp.price ORDER BY pp.created DESC), ',', 1) AS price FROM products p LEFT JOIN categories c ON (p.category_id = c.id) LEFT JOIN product_prices pp FORCE INDEX FOR JOIN (product_id) ON (p.id = pp.product_id) WHERE c.id = :category_id GROUP BY p.id LIMIT :offset, :limit");
    $statement->bindValue(":category_id", $category_id, PDO::PARAM_INT);
}

$statement->bindValue(":offset", ($page - 1) * $page_size, PDO::PARAM_INT);
$statement->bindValue(":limit", $page_size, PDO::PARAM_INT);
$statement->execute();

$products = sl_template_escape_array_of_arrays($statement->fetchAll(PDO::FETCH_ASSOC));

sl_template_render_header();
sl_template_render_sidebar();
sl_render_products($products, $category_id, "/products", $page, $page_size, $total_pages);
sl_template_render_footer();
