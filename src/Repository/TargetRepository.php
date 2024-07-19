<?php

declare(strict_types=1);

namespace App\Repository;

interface TargetRepository
{
    /** @param \App\Repository\Transaction[] $transactions */
    public function pushTransactions(array $transactions): void;
}