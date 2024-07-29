<?php

declare(strict_types=1);

namespace App\Resolver;

use App\Repository\SourceTransaction;
use App\Repository\TargetTransaction;

class ComGate implements TransactionResolver
{
    //Nákup: ComGate*Firma sro,  Adresa 123, Mesto, 123 45, CZE, dne 1.1.2024, částka  1000.00 CZK
    public function resolve(SourceTransaction $source, TargetTransaction $target): void
    {
        if ($source->userIdentification && str_starts_with($source->userIdentification, 'Nákup: ComGate*')) {
            preg_match('/^Nákup: ComGate\*(.*),.*,.*dne (\d+\.\d+\.\d+),.*$/uU', $source->userIdentification, $matches);

            // Take store name as Payee name
            if (isset($matches[1])) {
                $target->payeeName = $matches[1];
            }
        }
    }
}