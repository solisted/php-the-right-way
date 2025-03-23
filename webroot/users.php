<?php
declare(strict_types=1);

require("../includes/errors.php");
require("../includes/database.php");
require("../includes/request.php");
require("../includes/template.php");

function sl_render_users(array $users): void
{
    require("../templates/users.php");
}

sl_request_method_assert("GET");

$connection = sl_database_get_connection();

$statement = $connection->prepare("SELECT id, username, first_name, last_name, email FROM users");
$statement->execute();
$users = sl_template_escape_array_of_arrays($statement->fetchAll(PDO::FETCH_ASSOC));

sl_render_users($users);
