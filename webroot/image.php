<?php
declare(strict_types=1);

require("../includes/errors.php");
require("../includes/authentication.php");
require("../includes/authorization.php");
require("../includes/database.php");
require("../includes/request.php");
require("../includes/template.php");

sl_request_method_assert("GET");

$image_id = sl_request_query_get_integer("id", 0, PHP_INT_MAX);

$connection = sl_database_get_connection();

$statement = $connection->prepare("SELECT filename, orig_filename, mime_type FROM images WHERE id = :id");
$statement->bindValue(":id", $image_id, PDO::PARAM_INT);
$statement->execute();

if ($statement->rowCount() !== 1) {
    sl_request_terminate(404);
}

$image = sl_template_escape_array($statement->fetch(PDO::FETCH_ASSOC));

$image_filename = basename($image['filename']);
$image_orig_filename = basename($image['orig_filename']);

header("Content-Type: {$image['mime_type']}");
header("Content-Disposition: inline; filename=\"{$image_orig_filename}\"");
header("X-Accel-Redirect: /images/{$image_filename}");
