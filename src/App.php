<?php

declare(strict_types=1);

namespace App;

require_once __DIR__ . '/../config/env.php';

use App\Resolver\TransactionResolver;
use App\Repository\FioSourceRepository;
use App\Repository\SourceRepository;
use App\Repository\TargetRepository;
use App\Repository\YnabTargetRepository;
use RuntimeException;

class App
{
    private array $container = [];

    private function __construct()
    {
        $this->container[TransactionResolver::class] = [
            new Resolver\BankTransfer(),
            new Resolver\CardPayment(), // Keep this above other card payments
            new Resolver\GoPay(),
            new Resolver\ComGate(),
        ];

        $this->container[TargetRepository::class] =
        $this->container[YnabTargetRepository::class] = [
            new YnabTargetRepository(
                token: YNAB_API_TOKEN,
                budgetId: YNAB_BUDGET_ID,
                accountId: YNAB_ACCOUNT_ID,
            ),
        ];

        $this->container[SourceRepository::class] =
        $this->container[FioSourceRepository::class] = [
            new FioSourceRepository(
                token: FIO_API_TOKEN,
            ),
        ];

        $this->container[AdapterService::class] = [
            new AdapterService(
                sourceRepository: $this->getService(SourceRepository::class),
                targetRepository: $this->getService(TargetRepository::class),
                transactionResolvers: $this->getServices(TransactionResolver::class),
            ),
        ];
    }

    public static function boot(): self
    {
        return new self();
    }

    /**
     * @template T
     * @param T $type
     * @return T
     */
    public function getService(string $type)
    {
        $instances = $this->container[$type];

        if (!$instances) {
            throw new RuntimeException('No instances found');
        }

        if (count($instances) > 1) {
            throw new RuntimeException('Multiple instances found');
        }

        return $instances[0];
    }

    /**
     * @template T
     * @param T $type
     * @return array<T>
     */
    public function getServices(string $type): array
    {
        return $this->container[$type];
    }
}