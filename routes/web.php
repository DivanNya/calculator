<?php

declare(strict_types=1);

use app\modules\cost_calculator\controllers\api\v1\MonthsController;
use app\modules\cost_calculator\controllers\api\v1\PricesController;
use app\modules\cost_calculator\controllers\api\v1\RawTypesController;
use app\modules\cost_calculator\controllers\api\v1\TonnagesController;
use app\modules\cost_calculator\controllers\api\v2\MonthsController as MonthsControllerV2;
use app\modules\cost_calculator\controllers\api\v2\PricesController as PricesControllerV2;
use app\modules\cost_calculator\controllers\api\v2\RawTypesController as RawTypesControllerV2;
use app\modules\cost_calculator\controllers\api\v2\TonnagesController as TonnagesControllerV2;
use app\modules\cost_calculator\controllers\CalculatorController;
use Monoelf\Framework\http\router\HTTPRouterInterface;
use Monoelf\Framework\http\router\middlewares\JsonErrorHandlerMiddleware;
use Monoelf\Framework\http\router\middlewares\RequestLogMiddleware;
use Monoelf\Framework\http\router\middlewares\XApiKeyMiddleware;

/**
 * @var HTTPRouterInterface $router
 */
$router->addMiddleware(RequestLogMiddleware::class);

$router->get('/', CalculatorController::class . '::actionIndex');
$router->post('/', CalculatorController::class . '::actionCalculateByParams');

$router->group('api', function ($router): void {
    $router->group('v1', function ($router): void {
        $router->addResource('months', MonthsController::class);
        $router->addResource('types', RawTypesController::class);
        $router->addResource('tonnages', TonnagesController::class);
        $router->addResource('prices', PricesController::class);
    });
    $router->group('v2', function ($router): void {
        $router->addResource('months', MonthsControllerV2::class);
        $router->addResource('types', RawTypesControllerV2::class);
        $router->addResource('tonnages', TonnagesControllerV2::class);
        $router->addResource('prices', PricesControllerV2::class);
    });
})->addMiddleware(JsonErrorHandlerMiddleware::class)->addMiddleware(XApiKeyMiddleware::class);
