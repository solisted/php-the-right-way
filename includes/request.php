<?php
declare(strict_types=1);

function sl_request_terminate(int $http_code): void
{
    http_response_code($http_code);
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

function sl_request_post_get_integer(string $parameter_name, int $min, int $max, ?int $default = null): int
{
    return sl_request_get_integer(INPUT_POST, $parameter_name, $min, $max, $default);
}

function sl_request_get_post_parameters(array $parameters): array
{
    return filter_input_array(INPUT_POST, $parameters, true);
}
