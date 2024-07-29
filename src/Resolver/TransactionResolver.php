<?php

declare(strict_types=1);

namespace App\Resolver;

use App\Repository\SourceTransaction;
use App\Repository\TargetTransaction;

interface TransactionResolver
{
    public function resolve(SourceTransaction $source, TargetTransaction $target): void;
}