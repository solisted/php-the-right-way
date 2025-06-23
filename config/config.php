<?php
declare(strict_types=1);

$properties = [
    "SL_DATABASE_DSN",
    "SL_DATABASE_USER",
    "SL_DATABASE_PASSWORD",
];

foreach ($properties as $property) {
    define($property, getenv($property));
}

