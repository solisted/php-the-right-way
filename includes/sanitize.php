<?php
declare(strict_types=1);

function sl_sanitize_trim(string $value): string
{
    return mb_trim($value);
}

function sl_sanitize_case(string $value, int $case_type): string
{
    return mb_convert_case(mb_trim($value), $case_type);
}
