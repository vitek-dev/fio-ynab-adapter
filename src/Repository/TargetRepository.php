<?php

declare(strict_types=1);

namespace App\Repository;

interface TargetRepository
{
    /** @param list<\App\Repository\TargetTransaction> $transactions */
    public function pushTransactions(array $transactions): void;
}