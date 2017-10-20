<?php

use Slim\HttpCache\Cache;
use Slim\Middleware\HttpBasicAuthentication;

$slim->add(new HttpBasicAuthentication([
    'relaxed' => [],
    'path' => '/addProject',
    'secure' => $slim->getContainer()->get('shouldSecure'),
    'users' => $slim->getContainer()->get('authorizedUser')
]));

$slim->add(new Cache('public', 86400));
