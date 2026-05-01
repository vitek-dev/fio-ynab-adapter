<?php

declare(strict_types=1);

namespace App\Repository;

interface SourceRepository
{
    /**
     * @return list<\App\Repository\SourceTransaction>
     */
    public function fetchTransactions(): array;
}