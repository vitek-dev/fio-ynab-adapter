<?php

declare(strict_types=1);

namespace App;

use App\Repository\SourceRepository;
use App\Repository\TargetRepository;

final readonly class AdapterService
{
    public function __construct(
        private SourceRepository $sourceRepository,
        private TargetRepository $targetRepository,
    )
    {
    }

    public function run(): void
    {
        $this->targetRepository->pushTransactions(
            $this->sourceRepository->fetchTransactions(),
        );
    }
}