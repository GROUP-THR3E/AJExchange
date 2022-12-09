<?php

use GroupThr3e\AJExchange\Util\HomeDataset;
use GroupThr3e\AJExchange\Util\View;
use Slim\App;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

return function(App $app) {
    $app->get('/', function (Request $request, Response $response) {
        $homeDataset = new HomeDataset();
        $homeContent = $homeDataset->getHomepage();
        $view = View::render('index', ['data' => $homeContent]);
        $response->getBody()->write($view);
        return $response;
    });
};