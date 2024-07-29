<?php

declare(strict_types=1);

namespace App\Repository;

interface SourceRepository
{
    /**
     * @return \App\Repository\SourceTransaction[]
     */
    public function fetchTransactions(): array;
}