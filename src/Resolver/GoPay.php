<?php

declare(strict_types=1);

namespace App\Resolver;

use App\Repository\SourceTransaction;
use App\Repository\TargetTransaction;

class GoPay implements TransactionResolver
{
    //Nákup: GOPAY *MUJRANDOMESHOP.CZ,  ADRESA 123, PRAHA 7, 17000, CZE, dne 1.1.2024, částka  300.00 CZK =>> MUJRANDOMESHOP.CZ
    public function resolve(SourceTransaction $source, TargetTransaction $target): void
    {
        if ($source->userIdentification && str_starts_with($source->userIdentification, 'Nákup: GOPAY  *')) {
            preg_match('/^Nákup: GOPAY  \*(.*),.*dne (\d+\.\d+\.\d+),.*$/uU', $source->userIdentification, $matches);

            // Take store name as Payee name
            if (isset($matches[1])) {
                $target->payeeName = $matches[1];
            }
        }
    }
}