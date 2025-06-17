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

sl_auth_assert_authorized("ListCategories");

$connection = sl_database_get_connection();
$categories = sl_template_escape_array_of_arrays(sl_database_get_categories_with_product_count($connection));

sl_template_render_header();
sl_template_render_sidebar();
sl_render_categories($categories);
sl_template_render_footer();
