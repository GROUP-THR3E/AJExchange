<?php

use GroupThr3e\AJExchange\Constants\ApprovalStatus;
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
        $listings = $dataset->searchListings($params['query'] ?? '', type: $params['type'] ?? null,officeId: null,userId: null, approvalStatus: ApprovalStatus::APPROVED);
        $view = View::render('listings/search', ['listings' => $listings, 'params' => $params]);
        $response->getBody()->write($view);
        return $response;
    });

    $app->get('/listings/adminControls', function (Request $request, Response $response) {
        $params = $request->getQueryParams();
        $dataset = new ListingDataset();
        $listings = $dataset->searchListings(approvalStatus: $params['approvalStatus']);
        $view = View::render('listings/adminControls', ['listings' => $listings, 'approvalStatus' => $params['approvalStatus']]);
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
        $images = $request->getUploadedFiles();
        $dataset->createListing(
            $params['inputTitle'],
            $params['description'],
            $params['listingType'] === 'sell' ? $params['inputPrice'] : null,
            $params['listingType'] === 'exchange' ? $params['inputItem'] : null,
            $params['listingType'],
            [],
            Auth::getAuthManager()->getUser()->getUserId(),
            $images
        );
        $view = View::render('/listings/createSuccessful');
        $response->getBody()->write($view);
        return $response;
    });
    
    $app->post('/listings/{listingId:[0-9]+}/setApproval', function (Request $request, Response $response, array $args) {
        // Ensures the user is authorised to approve/deny listings
        if (Auth::getAuthManager()->getUser()->getRole() != 'admin') return $response->withStatus('403');

        // Ensures the given status code has a valid value
        $params = $request->getParsedBody();
        if ($params['approvalStatus'] !== ApprovalStatus::APPROVED && $params['approvalStatus'] !== ApprovalStatus::DENIED
            && $params['approvalStatus'] !== ApprovalStatus::PENDING) {
            return $response->withHeader('Location', "/listings/{$args['listingId']}")->withStatus(302);
        }

        $listingDataset = new ListingDataset();
        $listingDataset->setApproval($args['listingId'], $params['approvalStatus']);
        return $response->withHeader('Location', '/listings/adminControls')->withStatus(302);
    });
};