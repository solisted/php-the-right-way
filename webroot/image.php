<?php
declare(strict_types=1);

require("../includes/errors.php");
require("../includes/authentication.php");
require("../includes/authorization.php");
require("../includes/database.php");
require("../includes/request.php");

sl_request_method_assert("GET");

$image_id = sl_request_query_get_integer("id", 0, PHP_INT_MAX);

$connection = sl_database_get_connection();

$statement = $connection->prepare("SELECT filename FROM images WHERE id = :id");
$statement->bindValue(":id", $image_id, PDO::PARAM_INT);
$statement->execute();

if ($statement->rowCount() !== 1) {
    sl_request_terminate(404);
}

$image = $statement->fetch(PDO::FETCH_ASSOC);
$image_file_name = basename($image['filename']);

header("Content-Type: application/octet-stream");
header("X-Accel-Redirect: /images/{$image_file_name}");
