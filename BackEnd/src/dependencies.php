<?php

/**
 * This file contains the configuration of the slim Dependency Injection Component
 * @see https://www.slimframework.com/docs/concepts/di.html
 */

use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Formatter\LineFormatter;

$container = $slim->getContainer();

// Contains the global logger
$container['logger'] = function () {
    $logger = new Logger('logger');
    $handler = new ErrorLogHandler();
    $handler->setFormatter(new LineFormatter('[%datetime%] %channel%.%level_name%: %message%'));
    $logger->pushHandler($handler);
    return $logger;
};

// Contains the cache provider
$container['cache'] = function () {
    return new \Slim\HttpCache\CacheProvider();
};

// Contains the projects controller
$container['projectController'] = function () use ($container) {
    return new \HostMyDocs\Controllers\ProjectController($container);
};
