<?php

use Slim\Middleware\HttpBasicAuthentication;

$slim->add(new HttpBasicAuthentication([
    'relaxed' => [],
    'path' => '/addProject',
    'secure' => $slim->getContainer()->get('shouldSecure'),
    'users' => $slim->getContainer()->get('authorizedUser')
]));
