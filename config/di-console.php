<?php

declare(strict_types=1);

use Monoelf\Framework\common\AliasManager;
use Monoelf\Framework\common\ErrorHandlerInterface;
use Monoelf\Framework\config_storage\ConfigurationStorage;
use Monoelf\Framework\console\AnsiConsoleOutput;
use Monoelf\Framework\console\ConsoleErrorHandler;
use Monoelf\Framework\console\ConsoleInput;
use Monoelf\Framework\console\ConsoleInputInterface;
use Monoelf\Framework\console\ConsoleKernel;
use Monoelf\Framework\console\ConsoleKernelInterface;
use Monoelf\Framework\console\ConsoleOutputInterface;
use Monoelf\Framework\container\ContainerInterface;
use Monoelf\Framework\event_dispatcher\EventDispatcher;
use Monoelf\Framework\event_dispatcher\EventDispatcherInterface;
use Monoelf\Framework\logger\DebugTagGenerator;
use Monoelf\Framework\logger\DebugTagStorage;
use Monoelf\Framework\logger\DebugTagStorageInterface;
use Monoelf\Framework\logger\LogContextEvent;
use Monoelf\Framework\logger\LoggerInterface;
use Monoelf\Framework\logger\StdOutLogger;
use Monoelf\Framework\view\View;
use Monoelf\Framework\view\ViewInterface;

return [
    'definitions' => [],
    'singletons' => [
        ViewInterface::class => function (ContainerInterface $container): ViewInterface {
            return $container->build(View::class, ['rootPath' => '@app']);
        },
        EventDispatcherInterface::class => function (ContainerInterface $container): EventDispatcherInterface {
            $dispatcher = $container->get(EventDispatcher::class);
            $dispatcher->configure([
                LogContextEvent::ATTACH_CONTEXT => [[LoggerInterface::class, 'handle']],
                LogContextEvent::DETACH_CONTEXT => [[LoggerInterface::class, 'handle']],
                LogContextEvent::FLUSH_CONTEXT => [[LoggerInterface::class, 'handle']],
                LogContextEvent::ATTACH_CATEGORY => [[LoggerInterface::class, 'handle']],
                LogContextEvent::FLUSH_CATEGORY => [[LoggerInterface::class, 'handle']],
                LogContextEvent::ATTACH_EXTRAS => [[LoggerInterface::class, 'handle']],
                LogContextEvent::FLUSH_EXTRAS => [[LoggerInterface::class, 'handle']]
            ]);

            return $dispatcher;
        },
        DebugTagStorageInterface::class => DebugTagStorage::class,
        DebugTagGenerator::class => function (): DebugTagGenerator {
            return new DebugTagGenerator(true);
        },
        LoggerInterface::class => function (
            ContainerInterface $container,
            ConfigurationStorage $configStorage
        ): LoggerInterface {
            return $container->build(StdOutLogger::class, [
                'actionType' => 'cli',
                'projectIndex' => $configStorage->get('DOCKER_PROJECT')
            ]);
        },
        ConsoleInputInterface::class => ConsoleInput::class,
        ConsoleOutputInterface::class => AnsiConsoleOutput::class,
        ErrorHandlerInterface::class => ConsoleErrorHandler::class,
        ConsoleKernelInterface::class => function (
            ContainerInterface $container,
            ConfigurationStorage $configStorage
        ): ConsoleKernelInterface {
            return $container->build(ConsoleKernel::class, [
                'appName' => $configStorage->getOrDefault('APP_NAME', null),
                'version' => $configStorage->getOrDefault('DOCKER_VERSION', null),
                'inputPlugins' => $configStorage->getOrDefault('input-plugins', []),
                'modules' => $configStorage->getOrDefault('modules', []),
            ]);
        },
        ConfigurationStorage::class => function (): ConfigurationStorage {
            return ConfigurationStorage::create(require_once PROJECT_ROOT . '/config/console.php');
        },
        AliasManager::class => AliasManager::class,
    ],
];
