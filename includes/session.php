<?php
declare(strict_types=1);

function sl_session_set_flash_message(string $message): void
{
    $_SESSION["flash_message"] = $message;
}
