<?php

use GroupThr3e\AJExchange\Util\ListingDataset;
use GroupThr3e\AJExchange\Util\View;
use Slim\App;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

return function(App $app) {

    $app->get('/listings/search', function (Request $request, Response $response) {
        $dataset = new ListingDataset();
        $params = $request->getQueryParams();
        $listings = $dataset->searchListings($params['query']);
        $view = View::render('listings/search', ['listings' => $listings]);
        $response->getBody()->write($view);
        return $response;
    });


    $app->get('/listings', function (Request $request, Response $response) {
        $view = View::render('/listings/view');
        $response->getBody()->write($view);
        return $response;
    });

    $app->get('/listings/create', function (Request $request, Response $response) {
        $view = View::render('/listings/create');
        $response->getBody()->write($view);
        return $response;
    });
};
