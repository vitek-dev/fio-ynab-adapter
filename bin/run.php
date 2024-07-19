<?php

// Automatically fetch transactions from source (Fio) and transfer them to target (YNAB)
// Usage: `php run.php`

require_once __DIR__ . '/../vendor/autoload.php';

\App\App::boot()
    ->getService(\App\AdapterService::class)
    ->run();