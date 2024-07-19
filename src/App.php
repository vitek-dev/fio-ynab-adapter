<?php

declare(strict_types=1);

namespace App;

require_once __DIR__ . '/../config/env.php';

use App\Payee\PayeeNameResolver;
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
        $this->container[PayeeNameResolver::class] = [
            new Payee\GoPay(),
            new Payee\ComGate(),
            new Payee\UserIdentificationMap(),

            new Payee\CardPayment(), // Keep this one last
        ];

        $this->container[TargetRepository::class] =
        $this->container[YnabTargetRepository::class] = [
            new YnabTargetRepository(
                YNAB_API_TOKEN,
                YNAB_BUDGET_ID,
                YNAB_ACCOUNT_ID,
                $this->getServices(PayeeNameResolver::class),
            ),
        ];

        $this->container[SourceRepository::class] =
        $this->container[FioSourceRepository::class] = [
            new FioSourceRepository(
                FIO_API_TOKEN,
            ),
        ];

        $this->container[AdapterService::class] = [
            new AdapterService(
                $this->getService(SourceRepository::class),
                $this->getService(TargetRepository::class),
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