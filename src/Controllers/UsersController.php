<?php

use GroupThr3e\AJExchange\Util\Auth;
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
            $view = View::render('users/login', ['error' => $loginResult]);
            $response->getBody()->write($view);
        } else {
            $response = $response->withHeader('Location', '/')->withStatus(302);
        }

        return $response;
    });
};