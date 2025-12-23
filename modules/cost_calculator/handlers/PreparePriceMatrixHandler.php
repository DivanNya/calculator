<?php

declare(strict_types=1);

namespace app\modules\cost_calculator\handlers;

use app\modules\cost_calculator\dto\PriceMatrixDTO;

final class PreparePriceMatrixHandler
{
    public function handle(PriceMatrixDTO $dto): void
    {
        $filteredPrices = array_filter(
            $dto->prices,
            fn (array $row): bool => $row['raw_type_id'] === $dto->rawTypeId
        );

        $prices = [];

        foreach ($filteredPrices as $price) {
            $prices[$dto->months[$price['month_id']]][$dto->tonnages[$price['tonnage_id']]] = $price['price'];
        }

        foreach ($dto->months as $month) {
            $priceRow = [];

            foreach ($dto->tonnages as $tonnage) {
                $priceRow[$tonnage] = isset($prices[$month][$tonnage]) === true
                    ? $prices[$month][$tonnage]
                    : null;
            }

            $dto->priceMatrix[$month] = $priceRow;
        }
    }
}
