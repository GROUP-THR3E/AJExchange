<?php

use GroupThr3e\AJExchange\Util\View;
use Slim\Factory\AppFactory;

require_once '../vendor/autoload.php';

$app = AppFactory::create();
View::setPath('../src/Views');

// Imports each controller file and runs it
foreach (glob('../src/Controllers/*.php') as $filename) {
    $controller = require_once $filename;
    $controller($app);
}

$app->run();