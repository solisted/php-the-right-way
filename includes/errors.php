<?php
declare(strict_types=1);

ob_start();

function sl_exception_handler(Throwable $exception): void
{
    ob_end_clean();
    http_response_code(500);
    header("Content-Type: text/plain");

    if (SL_APPLICATION_DEBUG == 1) {
        print($exception);
    } else {
        print("Internal server error");
    }

    exit();
}

function sl_error_handler(int $code, string $message, string $file, int $line): void
{
    ob_end_clean();
    http_response_code(500);
    header("Content-Type: text/plain");

    if (SL_APPLICATION_DEBUG == 1) {
        print("Error: ${message} in ${file}:${line}\n");
        debug_print_backtrace();
    } else {
        print("Internal server error");
    }

    exit();
}

set_exception_handler("sl_exception_handler");
set_error_handler("sl_error_handler");
