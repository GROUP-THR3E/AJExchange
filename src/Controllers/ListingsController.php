<?php

use GroupThr3e\AJExchange\Util\Auth;
use GroupThr3e\AJExchange\Util\ListingDataset;
use GroupThr3e\AJExchange\Util\View;
use Slim\App;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

return function(App $app) {
    $app->get('/listings/search', function (Request $request, Response $response) {
        $dataset = new ListingDataset();
        $params = $request->getQueryParams();
        $listings = $dataset->searchListings($params['query'] ?? '', [], $params['type']);
        $view = View::render('listings/search', ['listings' => $listings, 'params' => $params]);
        $response->getBody()->write($view);
        return $response;
    });

    $app->get('/listings/{listingId:[0-9]+}', function (Request $request, Response $response, array $args) {
        $dataset = new ListingDataset();
        $listing = $dataset->getListing($args['listingId']);
        $view = View::render('/listings/view', ['listing' => $listing]);
        $response->getBody()->write($view);
        return $response;
    });

    $app->get('/listings/create', function (Request $request, Response $response) {
        $view = View::render('/listings/create');
        $response->getBody()->write($view);
        return $response;
    });

    $app->post('/listings/create', function (Request $request, Response $response) {
        $dataset = new ListingDataset();
        $params = $request->getParsedBody();
        $dataset->createListing(
            $params['inputTitle'],
            $params['description'],
            $params['listingType'] === 'sell' ? $params['inputPrice'] : null,
            $params['listingType'] === 'exchange' ? $params['inputItem'] : null,
            $params['listingType'],
            [],
            Auth::getAuthManager()->getUser()->getUserId()
        );
        $view = View::render('/listings/createSuccessful');
        $response->getBody()->write($view);
        return $response;
    });
};