<?php
declare(strict_types=1);

require("../config/config.php");
require("../includes/errors.php");
require("../includes/authentication.php");
require("../includes/authorization.php");
require("../includes/database.php");
require("../includes/request.php");
require("../includes/template.php");

function sl_render_users(array $users, string $url, int $page, int $size, int $total_pages): void
{
    require("../templates/users.php");
}

sl_request_method_assert("GET");

sl_auth_assert_authorized("ListUsers");

$page = sl_request_query_get_integer("page", 1, PHP_INT_MAX, 1);
$page_size = sl_request_query_get_integer("size", 10, 100, 15);

$connection = sl_database_get_connection();

$statement = $connection->query("SELECT COUNT(*) FROM users");
$row_count = $statement->fetchColumn(0);

$total_pages = intval(ceil($row_count / $page_size));
if ($total_pages > 0 && $page > $total_pages) {
    sl_request_terminate(400);
}

$statement = $connection->prepare(
   "SELECT
        u.id, u.username, u.first_name, u.last_name, u.email,
        SUBSTRING_INDEX(GROUP_CONCAT(us.id ORDER BY uh.created DESC), ',', 1) AS status_id,
        SUBSTRING_INDEX(GROUP_CONCAT(us.name ORDER BY uh.created DESC), ',', 1) AS status,
        SUBSTRING_INDEX(GROUP_CONCAT(uh.created ORDER BY uh.created DESC), ',', 1) AS updated
    FROM users u
    LEFT JOIN user_history uh ON (uh.user_id = u.id)
    LEFT JOIN user_statuses us ON (us.id = uh.status_id)
    GROUP BY u.id
    LIMIT :offset, :limit"
);
$statement->bindValue(":offset", ($page - 1) * $page_size, PDO::PARAM_INT);
$statement->bindValue(":limit", $page_size, PDO::PARAM_INT);
$statement->execute();

$users = sl_template_escape_array_of_arrays($statement->fetchAll(PDO::FETCH_ASSOC));

sl_template_render_header();
sl_template_render_sidebar();
sl_render_users($users, "/users", $page, $page_size, $total_pages);
sl_template_render_footer();
