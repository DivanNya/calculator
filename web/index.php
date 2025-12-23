<?php

use Monoelf\Framework\container\DIContainer;
use Monoelf\Framework\http\HTTPKernelInterface;
use Monoelf\Framework\http\router\HTTPRouterInterface;

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

const PROJECT_ROOT = __DIR__ . '/..';

require_once PROJECT_ROOT . '/vendor/autoload.php';

$container = DiContainer::create(require_once PROJECT_ROOT . '/config/di-web.php');

$router = $container->get(HTTPRouterInterface::class);

require_once PROJECT_ROOT . '/routes/web.php';

$response = $container->call(HTTPKernelInterface::class, 'handle');

$response->send();

exit;
