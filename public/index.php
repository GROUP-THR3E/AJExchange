<?php

use GroupThr3e\AJExchange\Util\Auth;
use GroupThr3e\AJExchange\Util\View;
use Lcobucci\JWT\Signer\Key\InMemory;
use PSR7Sessions\Storageless\Http\SessionMiddleware;
use Slim\Factory\AppFactory;

require_once '../vendor/autoload.php';

session_start();
$app = AppFactory::create();
View::setPath('../src/Views');
$config = parse_ini_file('../config.ini');

$app->add([Auth::getAuthManager(), 'authenticateRequest']);

// Imports each controller file and runs it
foreach (glob('../src/Controllers/*.php') as $filename) {
    $controller = require_once $filename;
    $controller($app);
}

$app->run();