<?php

declare(strict_types=1);

namespace App\Payee;

use App\Repository\Transaction;

class ComGate implements PayeeNameResolver
{
    //Nákup: ComGate*Firma sro,  Adresa 123, Mesto, 123 45, CZE, dne 1.1.2024, částka  1000.00 CZK
    public function resolve(Transaction $transaction): string|false
    {
        if ($transaction->userIdentification && str_starts_with($transaction->userIdentification, 'Nákup: ComGate*')) {
            preg_match('/^Nákup: ComGate\*(.*),.*$/uU', $transaction->userIdentification, $matches);

            if (isset($matches[1])) {
                return $matches[1];
            }
        }

        return false;
    }
}