<?php
declare(strict_types=1);

ob_start();

function sl_exception_handler(Throwable $exception): void
{
    ob_end_clean();
    http_response_code(500);
    header("Content-Type: text/plain");
    print($exception);
    exit();
}

function sl_error_handler(int $code, string $message, string $file, int $line): void
{
    ob_end_clean();
    http_response_code(500);
    header("Content-Type: text/plain");
    print("Error: ${message} in ${file}:${line}\n");
    debug_print_backtrace();
    exit();
}

set_exception_handler("sl_exception_handler");
set_error_handler("sl_error_handler");
