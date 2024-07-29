<?php

declare(strict_types=1);

namespace App\Repository;

use DateTimeImmutable;

final readonly class FioSourceRepository implements SourceRepository
{
    private const string FIO_API_URL = 'https://www.fio.cz/ib_api/rest/last/%s/transactions.json';

    public function __construct(
        private string $token,
    )
    {
    }

    /**
     * @throws \JsonException
     * @throws \Exception
     */
    public function fetchTransactions(): array
    {
        return array_map(
            fn(array $transaction) => $this->mapTransaction($transaction),
            $this->makeRequest(),
        );
    }

    public function reset(int $lastId): void
    {
        file_get_contents(
            sprintf(
                'https://fioapi.fio.cz/v1/rest/set-last-id/%s/%s/',
                FIO_API_TOKEN,
                $lastId,
            ),
        );
    }

    /**
     * @throws \JsonException
     */
    private function makeRequest(): array
    {
        $response = file_get_contents(
            sprintf(
                self::FIO_API_URL,
                $this->token,
            ),
        );

        return json_decode($response, true, 512, JSON_THROW_ON_ERROR)['accountStatement']['transactionList']['transaction'];
    }

    /**
     * @throws \Exception
     */
    private function mapTransaction(array $transaction): Transaction
    {
        return new Transaction(
            transactionId: (int)$transaction['column22']['value'],
            transactionType: $transaction['column8']['value'],
            date: new DateTimeImmutable($transaction['column0']['value']),
            amount: (float)$transaction['column1']['value'],
            userIdentification: $transaction['column7']['value'] ?? null,
            isCleared: true,
        );
    }
}