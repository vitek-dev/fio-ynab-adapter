<?php

declare(strict_types=1);

function env(string $name): string
{
    $value = getenv($name);

    if ($value === false || $value === '') {
        throw new RuntimeException("Missing required environment variable: $name");
    }

    return $value;
}

define('FIO_API_TOKEN', env('FIO_API_TOKEN'));
define('YNAB_API_TOKEN', env('YNAB_API_TOKEN'));
define('YNAB_BUDGET_ID', env('YNAB_BUDGET_ID'));
define('YNAB_ACCOUNT_ID', env('YNAB_ACCOUNT_ID'));
