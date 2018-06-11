<?php
ini_set('max_execution_time', 3600);

require 'vendor/autoload.php';

session_start();

$settings = require 'src/settings.php';

$slim = new \Slim\App($settings);

require 'src/dependencies.php';

require 'src/middleware.php';

require 'src/routes.php';

$slim->run();
