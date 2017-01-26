<?php

require 'vendor/autoload.php';

use Slim\Http\Response as Response;
use Slim\Http\Request as Request;

$slim = new \Slim\App();

$slim->get('/home', function (Request $request, Response $response) {
    return $response->write('It works !');
});

$slim->run();
