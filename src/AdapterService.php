<?php

declare(strict_types=1);

namespace App;

use App\Repository\SourceRepository;
use App\Repository\TargetRepository;
use App\Repository\SourceTransaction;
use App\Repository\TargetTransaction;

final readonly class AdapterService
{
    /**
     * @param \App\Resolver\TransactionResolver[] $transactionResolvers
     */
    public function __construct(
        private SourceRepository $sourceRepository,
        private TargetRepository $targetRepository,
        private array $transactionResolvers,
    ) {
    }

    public function run(): void
    {
        $transactions = $this->sourceRepository->fetchTransactions();

        $target = [];
        foreach ($transactions as $transaction) {
            $target[] = $this->resolveTransaction($transaction);
        }

        $this->targetRepository->pushTransactions($target);
    }

    private function resolveTransaction(SourceTransaction $source): TargetTransaction
    {
        $target = TargetTransaction::fromSource($source);

        foreach ($this->transactionResolvers as $resolver) {
            $resolver->resolve($source, $target);
        }

        $target->payeeName = mb_convert_case($target->payeeName, MB_CASE_TITLE, 'UTF-8');

        return $target;
    }
}