<?php

require 'vendor/autoload.php';

session_start();

$settings = require 'src/settings.php';

$slim = new \Slim\App($settings);

require 'src/routes.php';

$slim->run();
