<?php

declare(strict_types=1);

namespace app\modules\cost_calculator\controllers\api\v2;

use app\modules\cost_calculator\form_validators\DataBaseUniqueFormValidator;
use Monoelf\Framework\resource\AbstractResourceController;
use Monoelf\Framework\resource\DataBaseResourceDataFilter;
use Monoelf\Framework\resource\DataBaseResourceWriter;
use Monoelf\Framework\resource\form_request\FormRequest;
use Monoelf\Framework\resource\form_request\FormRequestFactoryInterface;
use Monoelf\Framework\resource\ResourceActionTypesEnum;
use Psr\Http\Message\ServerRequestInterface;

final class PricesController extends AbstractResourceController
{
    public function __construct(
        DataBaseResourceDataFilter $resourceDataFilter,
        ServerRequestInterface $request,
        FormRequestFactoryInterface $formRequestFactory,
        DataBaseResourceWriter $resourceWriter,
    ) {
        parent::__construct(
            $resourceDataFilter,
            $request,
            $formRequestFactory,
            $resourceWriter
        );
    }

    protected function getForms(): array
    {
        return [
            ResourceActionTypesEnum::UPDATE->value => [ FormRequest::class, [
                [['tonnage_id', 'month_id', 'raw_type_id', 'price'], 'required'],
                [['tonnage_id', 'month_id', 'raw_type_id', 'price'], 'int'],
                [['tonnage_id', 'month_id', 'raw_type_id'], [DataBaseUniqueFormValidator::class, 'resource' => $this->getResourceName()]]
            ]],
            ResourceActionTypesEnum::CREATE->value => [FormRequest::class, [
                [['tonnage_id', 'month_id', 'raw_type_id', 'price'], 'required'],
                [['tonnage_id', 'month_id', 'raw_type_id', 'price'], 'int'],
                [['tonnage_id', 'month_id', 'raw_type_id'], [DataBaseUniqueFormValidator::class, 'resource' => $this->getResourceName()]]
            ]],
            ResourceActionTypesEnum::PATCH->value => [FormRequest::class, [[['price'], 'int']]]
        ];
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
