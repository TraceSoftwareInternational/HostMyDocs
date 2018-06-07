<?php

use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Formatter\LineFormatter;

$container = $slim->getContainer();

$container['logger'] = function () {
    $logger = new Logger('logger');
    $handler = new ErrorLogHandler();
    $handler->setFormatter(new LineFormatter('[%datetime%] %channel%.%level_name%: %message%'));
    $logger->pushHandler($handler);
    return $logger;
};

$container['cache'] = function () {
    return new \Slim\HttpCache\CacheProvider();
};

$container['projectController'] = function () use ($container) {
    return new \HostMyDocs\Controllers\ProjectController($container);
};
