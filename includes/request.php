<?php
declare(strict_types=1);

function sl_request_terminate(int $http_code): void
{
    ob_end_clean();
    http_response_code($http_code);

    switch ($http_code) {
        case 404:
            require("../templates/header.php");
            require("../templates/404.php");
            require("../templates/footer.php");
    }

    exit();
}

function sl_request_redirect(string $url): void
{
    header("Location: ${url}");
    exit();
}

function sl_request_method_assert(string $method): void
{
    if (!isset($_SERVER["REQUEST_METHOD"]) || $_SERVER["REQUEST_METHOD"] !== $method) {
        sl_request_terminate(403);
    }
}

function sl_request_methods_assert(array $methods): void
{
    if (!isset($_SERVER["REQUEST_METHOD"]) || !in_array($_SERVER["REQUEST_METHOD"], $methods)) {
        sl_request_terminate(403);
    }
}

function sl_request_is_method(string $method): bool
{
    return isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] === $method;
}

function sl_request_is_uri(string $uri): bool
{
    return isset($_SERVER["REQUEST_URI"]) && mb_stripos($_SERVER["REQUEST_URI"], $uri) === 0;
}

function sl_request_get_integer(int $request_type, string $parameter_name, int $min, int $max, ?int $default = null): int
{
    $value = filter_input(
        $request_type,
        $parameter_name,
        FILTER_VALIDATE_INT,
        [
            "options" => ["min_range" => $min, "max_range" => $max],
            "flags" => FILTER_NULL_ON_FAILURE
        ]
    );

    if (($value === false && $default === null) || $value === null) {
        sl_request_terminate(400);
    }

    if ($value === false) {
        return $default;
    }

    return $value;
}

function sl_request_query_get_integer(string $parameter_name, int $min, int $max, ?int $default = null): int
{
    return sl_request_get_integer(INPUT_GET, $parameter_name, $min, $max, $default);
}

function sl_request_query_get_string(string $parameter_name, string $regexp, ?string $default = null): string
{
    $value = filter_input(INPUT_GET, $parameter_name, FILTER_SANITIZE_FULL_SPECIAL_CHARS, ["flags" => FILTER_NULL_ON_FAILURE]);

    if (($value === false && $default === null) || $value === null) {
        sl_request_terminate(400);
    }

    if ($value !== false && preg_match($regexp, $value) !== 1) {
        sl_request_terminate(400);
    }

    if ($value === false) {
        return $default;
    }

    return $value;
}

function sl_request_post_get_integer(string $parameter_name, int $min, int $max, ?int $default = null): int
{
    return sl_request_get_integer(INPUT_POST, $parameter_name, $min, $max, $default);
}

function sl_request_get_post_parameters(array $parameters): array
{
    return filter_input_array(INPUT_POST, $parameters, true);
}

function sl_request_post_string_equals(string $parameter, string $value)
{
    return isset($_POST[$parameter]) && $_POST[$parameter] === $value;
}
