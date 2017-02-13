<?php

use HostMyDocs\Controllers\AddProject;
use HostMyDocs\Controllers\ListProjects;
use Slim\Http\Response as Response;
use Slim\Http\Request as Request;

$slim->get('/', function (Request $request, Response $response) {
    return $response->write('What did you expect ?');
});


$slim->get('/listProjects', ListProjects::class);
$slim->post('/addProject', AddProject::class);
