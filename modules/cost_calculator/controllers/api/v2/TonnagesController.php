<?php

declare(strict_types=1);

namespace app\modules\cost_calculator\controllers\api\v2;

use app\modules\cost_calculator\form_validators\DataBaseUniqueFormValidator;
use Monoelf\Framework\resource\AbstractResourceController;
use Monoelf\Framework\resource\DataBaseResourceDataFilter;
use Monoelf\Framework\resource\DataBaseResourceWriter;
use Monoelf\Framework\resource\form_request\FormRequestFactoryInterface;
use Monoelf\Framework\resource\ResourceActionTypesEnum;
use Psr\Http\Message\ServerRequestInterface;

final class TonnagesController extends AbstractResourceController
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

    protected function getAvailableActions(): array
    {
        return [
            ResourceActionTypesEnum::INDEX,
            ResourceActionTypesEnum::CREATE,
            ResourceActionTypesEnum::DELETE,
        ];
    }

    protected function getFieldRules(): array
    {
        return [
            [['value'], 'required'],
            [['value'], 'int'],
            [['value'], [DataBaseUniqueFormValidator::class, 'resource' => $this->getResourceName()]],
        ];
    }

    protected function getResourceName(): string
    {
        return 'tonnages';
    }

    protected function getAccessibleFields(): array
    {
        return [
            'value',
            'id'
        ];
    }

    protected function getAccessibleFilters(): array
    {
        return ['id'];
    }
}
