<?php
/**
 * This file is used to register all routes
 */

use Slim\Http\Response as Response;
use Slim\Http\Request as Request;

$slim->get('/', function (Request $request, Response $response) {
    return $response->write('What did you expect ?');
});

// Includes all php files (recursive) in Routes folder
$dir = new RecursiveDirectoryIterator(__DIR__ . '/routes');
$iter = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($iter, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH); // an Iterator, not an array

foreach ($files as $file) {
    include $file[0];
}
