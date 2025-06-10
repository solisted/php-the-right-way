<?php
declare(strict_types=1);

function sl_sanitize_trim(string $value, bool $preserve_whitespace = false): string
{
    if ($preserve_whitespace === true) {
        return mb_trim($value);
    }

    return mb_trim(preg_replace("/\s+/u", " ", $value));
}

function sl_sanitize_case(string $value, int $case_type): string
{
    return mb_convert_case(sl_sanitize_trim($value), $case_type);
}

function sl_sanitize_filter(string $value, int $filter_type): string
{
    return filter_var($value, $filter_type);
}
