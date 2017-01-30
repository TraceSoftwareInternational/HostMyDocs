<?php

use Slim\Http\Response as Response;
use Slim\Http\Request as Request;

$slim->get('/', function (Request $request, Response $response) {
    return $response->write('HostMyDocs');
});
