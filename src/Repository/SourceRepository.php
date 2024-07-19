<?php

declare(strict_types=1);

namespace App\Repository;

interface SourceRepository
{
    /**
     * @return \App\Repository\Transaction[]
     */
    public function fetchTransactions(): array;
}