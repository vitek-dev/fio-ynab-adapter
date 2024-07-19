<?php

declare(strict_types=1);

namespace App\Payee;

use App\Repository\Transaction;

class CardPayment implements PayeeNameResolver
{
    //Nákup: MujObchod, ADRESA 123, PRAHA 7, 17000, CZE, dne 1.1.2024, částka 300.00 CZK
    public function resolve(Transaction $transaction): string|false
    {
        if ($transaction->userIdentification && str_starts_with($transaction->userIdentification, 'Nákup:')) {
            preg_match('/^Nákup: (.*),.*$/uU', $transaction->userIdentification, $matches);

            if (isset($matches[1])) {
                return $matches[1];
            }
        }

        return false;
    }
}