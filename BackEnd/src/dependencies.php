<?php

use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;

$container = $slim->getContainer();

$container['logger'] = function () {
    $logger = new Logger('logger');
    $logger->pushHandler(new ErrorLogHandler());
    $logger->info('The logger is ready');
    return $logger;
};

$container['cache'] = function () {
    return new \Slim\HttpCache\CacheProvider();
};

$container['projectController'] = function () use ($container) {
    return new \HostMyDocs\Controllers\ProjectController($container['storageRoot'], $container['archiveRoot']);
};
