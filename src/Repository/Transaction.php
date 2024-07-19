<?php

declare(strict_types=1);

namespace App\Repository;

use DateTimeImmutable;

final readonly class Transaction
{
    public function __construct(
        public int               $transactionId,
        public string            $transactionType,
        public DateTimeImmutable $date,
        public float             $amount,
        public ?string           $userIdentification = null,
    )
    {
        echo "Transaction found: {$this->amount} {$this->userIdentification}\n";
    }
}