<?php

declare(strict_types=1);

namespace App\Repository;

final readonly class YnabTargetRepository implements TargetRepository
{
    /**
     * @param \App\Payee\PayeeNameResolver[] $payeeNameResolvers
     */
    public function __construct(
        private string $token,
        private string $budgetId,
        private string $accountId,
        private array  $payeeNameResolvers,
    )
    {
    }

    /**
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
                'amount' => (int)($transaction->amount * 1000),
                'payee_name' => ucfirst($this->resolvePayeeName($transaction)),
                'memo' => $transaction->userIdentification,
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

    private function resolvePayeeName(Transaction $transaction): string
    {
        foreach ($this->payeeNameResolvers as $resolver) {
            $payeeName = $resolver->resolve($transaction);

            if ($payeeName !== false) {
                return $payeeName;
            }
        }

        return $transaction->transactionType;
    }
}