<?php
declare(strict_types=1);

define("SL_USER_INVALID_STATUS_ID", -1);
define("SL_USER_ACTIVE_STATUS_ID", 1);
define("SL_USER_LOCKED_STATUS_ID", 2);
define("SL_USER_DELETED_STATUS_ID", 3);

define("SL_ROLE_INVALID_STATUS_ID", -1);
define("SL_ROLE_ACTIVE_STATUS_ID", 1);
define("SL_ROLE_DELETED_STATUS_ID", 2);

$properties = [
    "SL_APPLICATION_DEBUG",
    "SL_DATABASE_DSN",
    "SL_DATABASE_USER",
    "SL_DATABASE_PASSWORD",
];

foreach ($properties as $property) {
    define($property, getenv($property));
}
