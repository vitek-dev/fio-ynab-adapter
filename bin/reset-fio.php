<?php

declare(strict_types=1);

// Manually set the last processed transaction ID
// Usage: `php reset-fio.php 12345`

require_once __DIR__ . '/../vendor/autoload.php';

$lastId = (int) ($argv[1] ?? throw new InvalidArgumentException('Missing argument'));

\App\App::boot()
    ->getService(\App\Repository\FioSourceRepository::class)
    ->reset($lastId);