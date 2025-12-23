<?php

declare(strict_types=1);

namespace app\modules\cost_calculator\services;

use app\modules\cost_calculator\dto\ListsDTO;
use Monoelf\Framework\resource\FileResourceDataFilter;

final readonly class GetListsService
{
    public function __construct(
        private FileResourceDataFilter $dataFilter,
    ) {}

    /**
     * @param ListsDTO $dto
     * @return void
     */
    public function handle(ListsDTO $dto): void
    {
        $this->dataFilter->setResourceName('months')->setAccessibleFields(['id', 'name']);
        $dto->months = array_column($this->dataFilter->filterAll([]), 'name', 'id');

        $this->dataFilter->setResourceName('raw_types')->setAccessibleFields(['id', 'name']);
        $dto->rawTypes = array_column($this->dataFilter->filterAll([]), 'name', 'id');

        $this->dataFilter->setResourceName('tonnages')->setAccessibleFields(['id', 'value']);
        $dto->tonnages = array_column($this->dataFilter->filterAll([]), 'value', 'id');

        $this->dataFilter->setResourceName('prices')->setAccessibleFields(['id', 'price', 'month_id', 'raw_type_id', 'tonnage_id']);
        $dto->prices = $this->dataFilter->filterAll([]);
    }
}
