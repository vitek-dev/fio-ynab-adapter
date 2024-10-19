<?php

declare(strict_types=1);

namespace App\Repository;

use DateTimeImmutable;

final readonly class SourceTransaction
{
    public function __construct(
        public string            $transactionId,
        public string            $transactionType,
        public ?string            $counterparty,
        public DateTimeImmutable $date,
        public float             $amount,
        public ?string           $userIdentification = null,
        public bool              $isCleared = false,
    ) {
        echo "Transaction found: {$this->amount} {$this->userIdentification}\n";
    }
}