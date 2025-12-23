<?php

declare(strict_types=1);

namespace app\modules\cost_calculator\controllers\api\v1;

use Monoelf\Framework\resource\AbstractResourceController;
use Monoelf\Framework\resource\FileResourceDataFilter;
use Monoelf\Framework\resource\FileResourceWriter;
use Monoelf\Framework\resource\form_request\FormRequestFactoryInterface;
use Monoelf\Framework\resource\ResourceActionTypesEnum;
use Psr\Http\Message\ServerRequestInterface;

final class TonnagesController extends AbstractResourceController
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
            ResourceActionTypesEnum::DELETE,
        ];
    }

    protected function getFieldRules(): array
    {
        return [
            [['value'], 'required'],
            [['value'], 'int'],
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
