<?php

declare(strict_types=1);

namespace app\modules\cost_calculator\services;

use app\modules\cost_calculator\dto\CalculatePriceDTO;
use Monoelf\Framework\resource\FileResourceDataFilter;

final readonly class CalculatePriceService
{
    public function __construct(
        private FileResourceDataFilter $dataFilter,
    ) {
        $this->dataFilter
            ->setResourceName('prices')
            ->setAccessibleFields(['price'])
            ->setAccessibleFilters(['month_id', 'raw_type_id', 'tonnage_id']);
    }

    public function handle(CalculatePriceDTO $dto): void
    {
        $price = $this->dataFilter->filterOne(['filter' => [
            'month_id' => $dto->monthId,
            'raw_type_id' => $dto->rawTypeId,
            'tonnage_id' => $dto->tonnageId,
        ]]);

        $dto->price = $price['price'] ?? null;
    }
}
