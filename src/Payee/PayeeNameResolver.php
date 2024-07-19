<?php

declare(strict_types=1);

namespace App\Payee;

use App\Repository\Transaction;

interface PayeeNameResolver
{
    public function resolve(Transaction $transaction): string|false;
}