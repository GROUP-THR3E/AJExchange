<?php

use GroupThr3e\AJExchange\Util\Auth;
use GroupThr3e\AJExchange\Util\ListingDataset;
use GroupThr3e\AJExchange\Util\OrderDataset;
use GroupThr3e\AJExchange\Util\View;
use Slim\App;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

return function(App $app) {
    $app->get('/users/login', function(Request $request, Response $response) {
        $view = View::render('users/login');
        $response->getBody()->write($view);
        return $response;
    });

    $app->post('/users/login', function(Request $request, Response $response) {
        $params = $request->getParsedBody();
        $auth = Auth::getAuthManager();
        $loginResult = $auth->authenticateCredentials($params['inputEmail'], $params['inputPassword'], $request);

        // Returns login form with an error if unsuccessful, redirects if successful
        if (is_string($loginResult)) {
            $view = View::render('users/login', ['error' => 'Incorrect Email or Password... try again!']);
            $response->getBody()->write($view);
        } else {
            $response = $response->withHeader('Location', '/')->withStatus(302);
        }

        return $response;
    });

    $app->post('/users/logout', function (Request $request, Response $response) {
        $auth = Auth::getAuthManager();
        $auth->logout();
        return $response->withHeader('Location', '/')->withStatus(302);
    });

    $app->get('/users/my/listings', function (Request $request, Response $response) {
        $params = $request->getQueryParams();
        $listingDataset = new ListingDataset();
        $listings = $listingDataset->searchListings(userId: Auth::getAuthManager()->getUser()->getUserId(), approvalStatus: $params['approvalStatus'] ?? null);
        $view = View::render('users/listings', ['listings' => $listings, 'approvalStatus' => $params['approvalStatus'] ?? null]);
        $response->getBody()->write($view);
        return $response;
    });

    $app->get('/users/my/orders', function (Request $request, Response $response) {
        $orderDataset = new OrderDataset();
        $orders = $orderDataset->getUserOrders(Auth::getAuthManager()->getUser()->getUserId());
        $view = View::render('users/orders', ['orders' => $orders]);
        $response->getBody()->write($view);
        return $response;
    });
};