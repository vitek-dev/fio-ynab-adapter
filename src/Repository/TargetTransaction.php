<?php

declare(strict_types=1);

namespace App\Repository;

use DateTimeImmutable;

final class TargetTransaction
{
    public function __construct(
        public string            $transactionId,
        public string            $transactionType,
        public string            $payeeName,
        public DateTimeImmutable $date,
        public float             $amount,
        public ?string           $note = null,
        public bool              $isCleared = false,
    ) {
    }

    public static function fromSource(SourceTransaction $transaction): self
    {
        return new self(
            transactionId: $transaction->transactionId,
            transactionType: $transaction->transactionType,
            payeeName: $transaction->transactionType,
            date: $transaction->date,
            amount: $transaction->amount,
            note: $transaction->userIdentification,
            isCleared: $transaction->isCleared,
        );
    }
}