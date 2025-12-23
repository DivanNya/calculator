<?php

declare(strict_types=1);

namespace app\modules\cost_calculator\dto;

final class PriceMatrixDTO
{
    public array $priceMatrix;

    public function __construct(
        public readonly array $prices,
        public readonly array $tonnages,
        public readonly array $months,
        public readonly int $rawTypeId,
    ) {}
}
