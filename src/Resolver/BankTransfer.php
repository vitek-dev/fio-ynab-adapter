<?php

declare(strict_types=1);

namespace App\Resolver;

use App\Repository\SourceTransaction;
use App\Repository\TargetTransaction;

final class BankTransfer implements TransactionResolver
{
    private const array BANK_TRANSFER_TYPES = [
        'Platba',
        'Příjem',
        'Bezhotovostní platba',
        'Bezhotovostní příjem',
        'Platba převodem uvnitř banky',
        'Příjem převodem uvnitř banky',
        'Okamžitá odchozí platba',
        'Okamžitá příchozí platba',
        'Okamžitá odchozí Europlatba',
        'Okamžitá příchozí Europlatba',
        'Platba v jiné měně',
        'Převod mezi bankovními konty (platba)',
        'Převod mezi bankovními konty (příjem)',
        'Vlastní platba z bankovního konta',
        'Vlastní příjem na bankovní konto',
        'Neidentifikovaná platba z bankovního konta',
        'Neidentifikovaný příjem na bankovní konto',
        'Inkaso',
        'Inkaso ve prospěch účtu',
        'Inkaso z účtu',
        'Příjem inkasa z cizí banky',
    ];

    #[\Override]
    public function resolve(SourceTransaction $source, TargetTransaction $target): void
    {
        if (in_array($source->transactionType, self::BANK_TRANSFER_TYPES, true)) {
            $payeeParts = array_filter([
                $source->counterpartyName,
                $source->counterparty,
            ]);

            if ($payeeParts) {
                $target->payeeName = implode(' - ', $payeeParts);
            } else {
                $target->payeeName = $source->transactionType;
            }
        }
    }
}