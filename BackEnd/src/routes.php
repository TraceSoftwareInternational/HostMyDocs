<?php

use HostMyDocs\Controllers\AddProject;
use HostMyDocs\Controllers\ListProjects;
use Slim\Http\Response as Response;
use Slim\Http\Request as Request;

$slim->get('/', function (Request $request, Response $response) {
    return $response->write('What did you expect ?');
});

$slim->group('/projects', function () {
    $this->get('', ListProjects::class);
    $this->post('', AddProject::class);
});
