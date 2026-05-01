<?php

declare(strict_types=1);

namespace App\Repository;

use DateTimeImmutable;

final readonly class FioSourceRepository implements SourceRepository
{
    private const string FETCH_URL = 'https://fioapi.fio.cz/v1/rest/last/%s/transactions.json';
    private const string SET_LAST_ID_URL = 'https://fioapi.fio.cz/v1/rest/set-last-id/%s/%s/';

    public function __construct(
        private string $token,
    ) {
    }

    /**
     * @return list<SourceTransaction>
     * @throws \JsonException
     * @throws \Exception
     */
    #[\Override]
    #[\NoDiscard]
    public function fetchTransactions(): array
    {
        return array_values(array_map($this->mapTransaction(...), $this->makeRequest()));
    }

    public function reset(int $lastId): void
    {
        file_get_contents(
            sprintf(
                self::SET_LAST_ID_URL,
                $this->token,
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
                self::FETCH_URL,
                $this->token,
            ),
        );

        return json_decode($response, true, 512, JSON_THROW_ON_ERROR)['accountStatement']['transactionList']['transaction'];
    }

    /**
     * @throws \Exception
     */
    private function mapTransaction(array $transaction): SourceTransaction
    {
        return new SourceTransaction(
            transactionId: (string) $transaction['column22']['value'],
            transactionType: $transaction['column8']['value'],
            counterparty: isset($transaction['column2']['value'], $transaction['column3']['value']) ?
                sprintf('%s/%s', $transaction['column2']['value'], $transaction['column3']['value']) :
                null,
            date: new DateTimeImmutable($transaction['column0']['value']),
            amount: (float)$transaction['column1']['value'],
            userIdentification: $transaction['column7']['value'] ?? null,
            isCleared: true,
        );
    }
}
