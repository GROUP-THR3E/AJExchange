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
        $listings = $dataset->searchListings($params['query'] ?? '');
        $view = View::render('listings/search', ['listings' => $listings]);
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
        $view = View::render('/listings/create' , ['errors' => [] , 'params' => []]);
        $response->getBody()->write($view);
        return $response;
    });

    $app->post('/listings/create', function (Request $request, Response $response) {
        $params = $request->getParsedBody();
        $dataset = new ListingDataset();
        $arrayError = [];

        if (trim($params['inputTitle'] == '')) {
            $arrayError[] = 'Your title is empty!';
        }
        elseif (strlen(trim($params['inputTitle'])) > 50) {
            $arrayError[] = 'Your title is too long! (Max characters: 50)';
        }

        if(trim($params['description'] == '')) {
            $arrayError[] = 'Your description is empty!';
        }
        elseif(strlen(trim($params['description'])) > 300) {
            $arrayError[] = 'Your description is too long! (Max characters: 300)';
        }

        if(!isset($params['listingType'])) {
            $arrayError[] ='You need to select one of the 3 exchange options!';
        }
        elseif(($params['listingType'] === 'sell')) {
            if(trim($params['inputPrice'] == '')) {
                $arrayError[] = 'Your sell amount is empty!';
            }
            elseif(!is_numeric(trim($params['inputPrice'])))
            {
                $arrayError[] = 'This is not a valid sell number!';
            }
            elseif(trim($params['inputPrice']) > 100000 || trim($params['inputPrice'] < 1)) {
                $arrayError[] = 'Your item is either too expensive or a negative value! (Min: £1 | Max: £100,000)';
            }
        }
        elseif(($params['listingType'] === 'exchange')) {
            if(trim($params['inputItem'] == '')) {
                $arrayError[] = 'Your exchange item is empty!';
            }
            elseif(strlen(trim($params['inputItem'])) > 50) {
                $arrayError[] = 'Your item name is too long! (Max characters: 50)';
            }
        }
        elseif(($params['listingType'] === 'giveaway')) {
            if (!isset($params['checkConf'])) {
                $arrayError[] ='You need to confirm your giveaway!';
            }
        }
        if(count($arrayError) > 0)
        {
            $view = View::render('/listings/create', ['errors' => $arrayError, 'params' => $params]);
        }

        else {
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
        }
        $response->getBody()->write($view);
        return $response;
    });
};