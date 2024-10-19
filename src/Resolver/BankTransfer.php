<?php

declare(strict_types=1);

namespace App\Resolver;

use App\Repository\SourceTransaction;
use App\Repository\TargetTransaction;

class BankTransfer implements TransactionResolver
{
    private const array BANK_TRANSFER_TYPES = [
        'Okamžitá odchozí platba',
        'Okamžitá příchozí platba',
        'Platba převodem uvnitř banky',
        'Bezhotovostní příjem',
    ];

    public function resolve(SourceTransaction $source, TargetTransaction $target): void
    {
        if (in_array($source->transactionType, self::BANK_TRANSFER_TYPES, true)) {
            $target->payeeName = $source->counterparty ?? $source->transactionType;
        }
    }
}