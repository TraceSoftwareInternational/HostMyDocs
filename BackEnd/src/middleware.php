<?php

use Slim\Middleware\HttpBasicAuthentication;

$slim->add(new HttpBasicAuthentication([
    'path' => '/addProject',
    'users' => $slim->getContainer()->get('authorizedUser')
]));
