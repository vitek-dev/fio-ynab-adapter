<?php

declare(strict_types=1);

namespace App\Resolver;

use App\Repository\SourceTransaction;
use App\Repository\TargetTransaction;
use DateTimeImmutable;

class CardPayment implements TransactionResolver
{
    //Nákup: MujObchod, ADRESA 123, PRAHA 7, 17000, CZE, dne 1.1.2024, částka 300.00 CZK
    public function resolve(SourceTransaction $source, TargetTransaction $target): void
    {
        if ($source->userIdentification && str_starts_with($source->userIdentification, 'Nákup:')) {
            preg_match('/^Nákup: (.*),.*dne (\d+\.\d+\.\d+),.*$/uU', $source->userIdentification, $matches);

            // Take store name as Payee name
            if (isset($matches[1])) {
                $target->payeeName = $matches[1];

                // If we have store name, we don't need userIdentification anymore
                $target->note = null;
            }

            // Take real payment date instead of processed date
            if (isset($matches[2])) {
                $date = DateTimeImmutable::createFromFormat('j.n.Y', $matches[2]);
                $target->date = $date;
            }
        }
    }
}