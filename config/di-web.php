<?php

declare(strict_types=1);

use Monoelf\Framework\common\AliasManager;
use Monoelf\Framework\common\ErrorHandlerInterface;
use Monoelf\Framework\config_storage\ConfigurationStorage;
use Monoelf\Framework\container\ContainerInterface;
use Monoelf\Framework\event_dispatcher\EventDispatcher;
use Monoelf\Framework\event_dispatcher\EventDispatcherInterface;
use Monoelf\Framework\http\error_handler\HttpErrorHandler;
use Monoelf\Framework\http\HttpKernel;
use Monoelf\Framework\http\HTTPKernelInterface;
use Monoelf\Framework\resource\DataBaseResourceDataFilter;
use Monoelf\Framework\resource\DataBaseResourceWriter;
use Monoelf\Framework\http\Response;
use Monoelf\Framework\http\router\HTTPRouterInterface;
use Monoelf\Framework\http\router\middlewares\XApiKeyMiddleware;
use Monoelf\Framework\http\router\Router;
use Monoelf\Framework\http\ServerRequest;
use Monoelf\Framework\http\ServerResponseInterface;
use Monoelf\Framework\http\Stream;
use Monoelf\Framework\http\Uri;
use Monoelf\Framework\logger\DebugTagGenerator;
use Monoelf\Framework\logger\DebugTagStorage;
use Monoelf\Framework\logger\DebugTagStorageInterface;
use Monoelf\Framework\logger\LogContextEvent;
use Monoelf\Framework\logger\LoggerInterface;
use Monoelf\Framework\logger\StdOutLogger;
use Monoelf\Framework\resource\connection\ConnectionFactory;
use Monoelf\Framework\resource\connection\ConnectionFactoryInterface;
use Monoelf\Framework\resource\connection\DataBaseConnection;
use Monoelf\Framework\resource\connection\JsonDataBaseConnection;
use Monoelf\Framework\resource\FileResourceDataFilter;
use Monoelf\Framework\resource\FileResourceWriter;
use Monoelf\Framework\resource\form_request\FormRequestFactoryInterface;
use Monoelf\Framework\resource\form_request\FormRequestFactory;
use Monoelf\Framework\resource\query\file\FileQueryBuilder;
use Monoelf\Framework\resource\query\file\FileQueryBuilderInterface;
use Monoelf\Framework\resource\query\mySQL\DataBaseQueryBuilder;
use Monoelf\Framework\resource\query\mySQL\DataBaseQueryBuilderInterface;
use Monoelf\Framework\view\View;
use Monoelf\Framework\view\ViewInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

return [
    'definitions' => [
        DataBaseResourceDataFilter::class => function (ContainerInterface $container): DataBaseResourceDataFilter {
            return new DataBaseResourceDataFilter(
                $container->get(DatabaseConnection::class),
                $container->get(DatabaseQueryBuilder::class),
            );
        },
        DataBaseResourceWriter::class => function (ContainerInterface $container): DataBaseResourceWriter {
            return new DataBaseResourceWriter($container->get(DatabaseConnection::class));
        },
        FileResourceDataFilter::class => function (ContainerInterface $container): FileResourceDataFilter {
            return new FileResourceDataFilter(
                $container->get(JsonDatabaseConnection::class),
                $container->get(FileQueryBuilderInterface::class),
            );
        },
        FileResourceWriter::class => function (ContainerInterface $container): FileResourceWriter {
            return new FileResourceWriter($container->get(JsonDataBaseConnection::class));
        }
    ],
    'singletons' => [
        ConnectionFactoryInterface::class => ConnectionFactory::class,
        FormRequestFactoryInterface::class => FormRequestFactory::class,
        DataBaseQueryBuilderInterface::class => DataBaseQueryBuilder::class,
        FileQueryBuilderInterface::class => FileQueryBuilder::class,

        ViewInterface::class => function (ContainerInterface $container): ViewInterface {
            return $container->build(View::class, [
                'rootPath' => '@app/views'
            ]);
        },
        HTTPRouterInterface::class => Router::class,
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
        LoggerInterface::class => function (
            ContainerInterface $container,
            ConfigurationStorage $configStorage
        ): LoggerInterface {
            return $container->build(StdOutLogger::class, [
                'actionType' => 'web',
                'projectIndex' => $configStorage->get('DOCKER_PROJECT')
            ]);
        },
        DebugTagGenerator::class => function (): DebugTagGenerator {
            return new DebugTagGenerator(false);
        },
        ServerResponseInterface::class => Response::class,
        ServerRequestInterface::class => function (): ServerRequestInterface {
            return ServerRequest::fromGlobals();
        },
        StreamInterface::class => function (): StreamInterface {
            return new Stream(fopen('php://temp', 'rbw+'));
        },
        UriInterface::class => Uri::class,
        HTTPKernelInterface::class => function (ContainerInterface $container): HTTPKernelInterface {
            $config = $container->get(ConfigurationStorage::class);

            return $container->build(HttpKernel::class, [
                'modules' => $config->getOrDefault('modules', []),
            ]);
        },
        ErrorHandlerInterface::class => HttpErrorHandler::class,
        ConfigurationStorage::class => function (): ConfigurationStorage {
            return ConfigurationStorage::create(require_once PROJECT_ROOT . '/config/common.php');
        },
        AliasManager::class => AliasManager::class,
        XApiKeyMiddleware::class => function (ConfigurationStorage $configStorage): XApiKeyMiddleware {
            return new XApiKeyMiddleware($configStorage->get("API_AUTH_KEY"));
        },
        DatabaseConnection::class => function (ConfigurationStorage $configStorage): DatabaseConnection {
            $config = [
                'host' => $configStorage->get('DB_HOST'),
                'dbname' => $configStorage->get('DB_NAME'),
                'username' => $configStorage->get('DB_USER'),
                'password' => $configStorage->get('DB_PASSWORD'),
                'charset' => 'utf8mb4'
            ];

            return new DatabaseConnection($config);
        },
        JsonDataBaseConnection::class => JsonDataBaseConnection::class,
    ],
];
