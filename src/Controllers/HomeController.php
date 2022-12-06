<?php

use GroupThr3e\AJExchange\Util\View;
use Slim\App;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

return function(App $app) {
    $app->get('/', function (Request $request, Response $response) {
        $view = View::render('index', ['name' => 'test']);
        $response->getBody()->write($view);
        return $response;
    });
};