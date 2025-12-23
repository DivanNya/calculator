<?php

declare(strict_types=1);

namespace app\modules\cost_calculator\controllers;

use app\modules\cost_calculator\dto\CalculatePriceDTO;
use app\modules\cost_calculator\dto\ListsDTO;
use app\modules\cost_calculator\dto\PriceMatrixDTO;
use app\modules\cost_calculator\handlers\PreparePriceMatrixHandler;
use app\modules\cost_calculator\services\CalculatePriceService;
use app\modules\cost_calculator\services\GetListsService;
use Exception;
use Monoelf\Framework\config_storage\ConfigurationStorage;
use Monoelf\Framework\event_dispatcher\EventDispatcherInterface;
use Monoelf\Framework\event_dispatcher\Message;
use Monoelf\Framework\http\dto\BaseControllerResponse;
use Monoelf\Framework\http\StatusCodeEnum;
use Monoelf\Framework\logger\DebugTagStorage;
use Monoelf\Framework\logger\LogContextEvent;
use Monoelf\Framework\logger\LoggerInterface;
use Monoelf\Framework\view\ViewInterface;
use Monoelf\Framework\view\ViewNotFoundException;
use Psr\Http\Message\ServerRequestInterface;

final class CalculatorController
{
    public function __construct(
        private readonly ViewInterface $viewRenderer,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly DebugTagStorage $debugTagStorage,
        private readonly LoggerInterface $logger,
        private readonly CalculatePriceService $calculatePriceService,
        private readonly PreparePriceMatrixHandler $priceMatrixHandler,
        private readonly GetListsService $getListsService,
        private readonly ConfigurationStorage $configurationStorage,
    ) {
        $this->eventDispatcher->trigger(LogContextEvent::ATTACH_CONTEXT, new Message("Калькулятор"));
        $this->eventDispatcher->trigger(LogContextEvent::ATTACH_CATEGORY, new Message(self::class));
    }

    /**
     * @throws ViewNotFoundException
     * @throws Exception
     */
    public function actionIndex(): string
    {
        if ($this->configurationStorage->get('RENDER_MODE') === 'SPA') {
            return file_get_contents(PROJECT_ROOT . '/web/index.html');
        }

        $this->eventDispatcher->trigger(
            LogContextEvent::ATTACH_CONTEXT,
            new Message("Форма расчета сырья")
        );
        $this->logger->info("Отображение формы");
        $this->eventDispatcher->trigger(
            LogContextEvent::DETACH_CONTEXT,
            new Message("Поиск подходящей последовательности операций")
        );

        $lists = new ListsDTO();
        $this->getListsService->handle($lists);

        return $this->viewRenderer->render('@views-web/index', [
            'xDebugTag' => $this->debugTagStorage->getTag(),
            'showTable' => false,
            'lists' => $lists,
            'paramsDto' => new CalculatePriceDTO(),
            'errorMessage' => null,
        ]);
    }

    /**
     * @throws ViewNotFoundException
     * @throws Exception
     */
    public function actionCalculateByParams(ServerRequestInterface $request): BaseControllerResponse|string
    {
        $this->eventDispatcher->trigger(
            LogContextEvent::ATTACH_CONTEXT,
            new Message("Форма расчета сырья")
        );
        $this->logger->info("Поиск цены по параметрам");

        $lists = new ListsDTO();
        $this->getListsService->handle($lists);

        $errorMessage = null;

        if (isset($request->getParsedBody()['month']) === false) {
            $errorMessage = 'Не заполнено поле Месяц';
        }

        if (isset($request->getParsedBody()['tonnage']) === false) {
            $errorMessage = ($errorMessage === null ? '' : "$errorMessage<br>") . 'Не заполнено поле Тоннаж';
        }

        if (isset($request->getParsedBody()['raw_type']) === false) {
            $errorMessage = ($errorMessage === null ? '' : "$errorMessage<br>") . 'Не заполнено поле Тип сырья';
        }

        if ($errorMessage !== null) {
            $this->logger->error($errorMessage);

            return new BaseControllerResponse(
                StatusCodeEnum::STATUS_NOT_FOUND->value,
                $this->viewRenderer->render('@views-web/index', [
                    'xDebugTag' => $this->debugTagStorage->getTag(),
                    'showTable' => false,
                    'lists' => $lists,
                    'paramsDto' => new CalculatePriceDTO(),
                    'errorMessage' => $errorMessage,
                ])
            );
        }

        $dto = new CalculatePriceDTO(
            (int)$request->getParsedBody()['month'],
            (int)$request->getParsedBody()['tonnage'],
            (int)$request->getParsedBody()['raw_type'],
        );

        $this->eventDispatcher->trigger(
            LogContextEvent::ATTACH_EXTRAS,
            new Message([
                'month' => $dto->monthId,
                'tonnage' => $dto->tonnageId,
                'rawType' => $dto->rawTypeId,
            ])
        );

        $this->logger->info('Запрос расчета стоимости');

        $this->eventDispatcher->trigger(LogContextEvent::FLUSH_EXTRAS);

        $this->calculatePriceService->handle($dto);

        if ($dto->price === null) {
            $errorMessage = "Стоимость для параметров месяц: {$lists->months[$dto->monthId]},
             тоннаж:  {$lists->tonnages[$dto->tonnageId]},
             тип сырья:  {$lists->rawTypes[$dto->rawTypeId]} не найдена";
        }

        $this->logger->info($dto->price === null ? 'Цена не найдена' : 'Найдена цена');

        $this->eventDispatcher->trigger(
            LogContextEvent::DETACH_CONTEXT,
            new Message('Поиск подходящей последовательности операций')
        );

        $matrix = new PriceMatrixDTO(
            $lists->prices,
            $lists->tonnages,
            $lists->months,
            $dto->rawTypeId,
        );
        $this->priceMatrixHandler->handle($matrix);

        return $this->viewRenderer->render('@views-web/index', [
            'xDebugTag' => $this->debugTagStorage->getTag(),
            'paramsDto' => $dto,
            'priceMatrix' => $matrix->priceMatrix,
            'lists' => $lists,
            'showTable' => true,
            'errorMessage' => $errorMessage,
            'view' => $this->viewRenderer,
        ]);
    }
}
