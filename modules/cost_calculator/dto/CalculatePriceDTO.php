<?php

declare(strict_types=1);

namespace app\modules\cost_calculator\dto;

final class CalculatePriceDTO {
    public ?int $price = null;

    public function __construct(
        public ?int $monthId = null,
        public ?int $tonnageId = null,
        public ?int $rawTypeId = null,
    ) {}
}
