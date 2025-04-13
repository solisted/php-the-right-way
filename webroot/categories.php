<?php
declare(strict_types=1);

require("../includes/errors.php");
require("../includes/authentication.php");
require("../includes/authorization.php");
require("../includes/database.php");
require("../includes/request.php");
require("../includes/template.php");

function sl_render_categories(array $categories): void
{
    require("../templates/categories.php");
}

sl_request_method_assert("GET");

$connection = sl_database_get_connection();

$statement = $connection->query("SELECT node.id, node.name, (COUNT(parent.id) - 1) AS depth FROM categories AS node, categories AS parent WHERE node.lft BETWEEN parent.lft AND parent.rgt GROUP BY node.id ORDER BY node.lft");
$statement->execute();

$categories = sl_template_escape_array_of_arrays($statement->fetchAll(PDO::FETCH_ASSOC));

sl_template_render_header();
sl_template_render_sidebar();
sl_render_categories($categories);
sl_template_render_footer();
