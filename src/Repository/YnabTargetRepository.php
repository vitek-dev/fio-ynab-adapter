<?php

declare(strict_types=1);

namespace App\Repository;

final readonly class YnabTargetRepository implements TargetRepository
{
    public function __construct(
        private string $token,
        private string $budgetId,
        private string $accountId,
    ) {
    }

    /**
     * @param \App\Repository\TargetTransaction[] $transactions
     * @throws \JsonException
     */
    public function pushTransactions(array $transactions): void
    {
        if (!$transactions) {
            return;
        }

        $data = [];

        foreach ($transactions as $transaction) {
            $data[] = [
                'account_id' => $this->accountId,
                'date' => $transaction->date->format('Y-m-d'),
                'cleared' => $transaction->isCleared ? 'cleared' : 'uncleared',
                'amount' => (int)($transaction->amount * 1000),
                'payee_name' => $transaction->payeeName,
                'memo' => $transaction->note,
                'import_id' => $transaction->transactionId,
            ];
        }

        file_get_contents(
            sprintf('https://api.youneedabudget.com/v1/budgets/%s/transactions', $this->budgetId),
            context: stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => implode("\r\n", [
                        sprintf('Authorization: Bearer %s', $this->token),
                        'Content-Type: application/json',
                    ]),
                    'content' => json_encode(
                        [
                            'transactions' => $data,
                        ],
                        JSON_THROW_ON_ERROR,
                    ),
                ],
            ]),
        );
    }
}