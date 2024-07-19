<?php

declare(strict_types=1);

namespace App\Payee;

use App\Repository\Transaction;

class UserIdentificationMap implements PayeeNameResolver
{
    //Nákup: ALBERT VAM DEKUJE, ADRESA 123, PRAHA 7, 17000, CZE, dne 1.1.2024, částka 300.00 CZK
    private const array MAP = [
        ': ALBERT VAM DEKUJE' => 'Albert',
        ': DEKUJEME, ROHLIK.CZ' => 'Rohlik',
        ': www.luxor.cz' => 'Luxor',
        ': JEDNOTA ' => 'Coop',
        ': BENU lekarna ' => 'Benu',
    ];

    public function resolve(Transaction $transaction): string|false
    {
        if (!$transaction->userIdentification) {
            return false;
        }

        foreach (self::MAP as $rule => $value) {
            if (str_contains($transaction->userIdentification, $rule)) {
                return $value;
            }
        }

        return false;
    }
}