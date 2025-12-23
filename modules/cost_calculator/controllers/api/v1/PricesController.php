<?php

declare(strict_types=1);

namespace app\modules\cost_calculator\controllers\api\v1;

use Monoelf\Framework\resource\AbstractResourceController;
use Monoelf\Framework\resource\FileResourceDataFilter;
use Monoelf\Framework\resource\FileResourceWriter;
use Monoelf\Framework\resource\form_request\FormRequestFactoryInterface;
use Monoelf\Framework\resource\ResourceActionTypesEnum;
use Psr\Http\Message\ServerRequestInterface;

final class PricesController extends AbstractResourceController
{
    public function __construct(
        FileResourceDataFilter $resourceDataFilter,
        ServerRequestInterface $request,
        FormRequestFactoryInterface $formRequestFactory,
        FileResourceWriter $resourceWriter,
    ) {
        parent::__construct(
            $resourceDataFilter,
            $request,
            $formRequestFactory,
            $resourceWriter
        );
    }

    protected function getAvailableActions(): array
    {
        return [
            ResourceActionTypesEnum::INDEX,
            ResourceActionTypesEnum::CREATE,
            ResourceActionTypesEnum::UPDATE,
            ResourceActionTypesEnum::PATCH,
            ResourceActionTypesEnum::DELETE,
        ];
    }

    protected function getFieldRules(): array
    {
        return [
            [['tonnage_id', 'month_id', 'raw_type_id', 'price'], 'required'],
            [['tonnage_id', 'month_id', 'raw_type_id', 'price'], 'int'],
        ];
    }

    protected function getResourceName(): string
    {
        return 'prices';
    }

    protected function getAccessibleFields(): array
    {
        return ['id', 'tonnage_id', 'month_id', 'raw_type_id', 'price'];
    }

    protected function getAccessibleFilters(): array
    {
        return ['id', 'tonnage_id', 'month_id', 'raw_type_id'];
    }
}
