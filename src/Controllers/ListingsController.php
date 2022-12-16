<?php

use GroupThr3e\AJExchange\Constants\ApprovalStatus;
use GroupThr3e\AJExchange\Models\Charity;
use GroupThr3e\AJExchange\Util\Auth;
use GroupThr3e\AJExchange\Util\CharityDataset;
use GroupThr3e\AJExchange\Util\ListingDataset;
use GroupThr3e\AJExchange\Util\OrderDataset;
use GroupThr3e\AJExchange\Util\View;
use Slim\App;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

return function(App $app) {
    $app->get('/listings/search', function (Request $request, Response $response) {
        $dataset = new ListingDataset();
        $params = $request->getQueryParams();
        $listings = $dataset->searchListings(query: $params['query'] ?? '', type: $params['type'] ?? null,
            approvalStatus: ApprovalStatus::APPROVED, hideOwnListings: true, showOrdered: false);
        $view = View::render('listings/search', ['listings' => $listings, 'params' => $params]);
        $response->getBody()->write($view);
        return $response;
    });

    $app->get('/listings/adminControls', function (Request $request, Response $response) {
        $params = $request->getQueryParams();
        $dataset = new ListingDataset();
        $listings = $dataset->searchListings(approvalStatus: $params['approvalStatus'] ?? null);
        if (strcmp('admin',Auth::getAuthManager()->getUser()->getRole()) === 0) {
            $view = View::render('listings/adminControls', ['listings' => $listings, 'approvalStatus' => $params['approvalStatus'] ?? '']);
            $response->getBody()->write($view);
        } else {
            return $response->withHeader('Location', '/')->withStatus(302);
        }
        return $response;
    });

    $app->get('/listings/{listingId:[0-9]+}', function (Request $request, Response $response, array $args) {
        $dataset = new ListingDataset();
        $listing = $dataset->getListing($args['listingId']);
        $currentUser = Auth::getAuthManager()->getUser();
        if ($listing->getOrderId() !== null && ($listing->getUserId() !== $currentUser->getUserId()
                || $currentUser->getRole() === 'admin')) {
            $view = View::render('messageLink', [
                'pageTitle' => 'Listing error',
                'message' => 'This listing has been ordered',
                'linkMessage' => 'Click here to return to home',
                'linkhref' => '/'
            ]);
        } else if ($listing->getApprovalStatus() !== ApprovalStatus::APPROVED
                && ($currentUser->getUserId() !== $listing->getUserId() && $currentUser->getRole() !== 'admin')) {
            return $response->withHeader('Location', '/')->withStatus(302);
        } else {
            $view = View::render('/listings/view', ['listing' => $listing]);
        }

        $response->getBody()->write($view);
        return $response;
    });

    $app->get('/listings/create', function (Request $request, Response $response) {
        $charityDataset = new CharityDataset();
        $charities = $charityDataset->getCharities();
        $view = View::render('/listings/create' , ['errors' => [] , 'params' => [], 'charities' => $charities]);
        $response->getBody()->write($view);
        return $response;
    });

    $app->post('/listings/create', function (Request $request, Response $response) {
        $params = $request->getParsedBody();
        $images = $request->getUploadedFiles();
        $dataset = new ListingDataset();
        $arrayError = [];
        $charityDataset = new CharityDataset();
        $charities = $charityDataset->getCharities();
        $charityIds = array_map(function(Charity $charity) { return $charity->getCharityId(); }, $charities);
        $imageError = true;
        $tagsString = trim($params['inputTags'],", \n\r\t\v\x00");
        $tagsString = preg_replace("/\s*,+\s*/", ',' ,$tagsString);
        $tags = explode(',', $tagsString);

        foreach($images as $image )
        if($image->getError() === 0)
        {
            $imageError = false;
            break;
        }

        if (trim($params['inputTitle'] == '')) {
            $arrayError[] = 'Your title is empty!';
        }
        elseif (strlen(trim($params['inputTitle'])) > 50) {
            $arrayError[] = 'Your title is too long! (Max characters: 50)';
        }

        if(trim($params['description'] == '')) {
            $arrayError[] = 'Your description is empty!';
        }
        elseif(strlen(trim($params['description'])) > 200) {
            $arrayError[] = 'Your description is too long! (Max characters: 300)';
        }

        if($imageError) {
            $arrayError[] = 'You must select at least 1 image!';
        }

        if(!isset($params['listingType'])) {
            $arrayError[] ='You need to select one of the 3 trade options!';
        }
        elseif((isset($params['checkCharity']) && ($params['listingType'] !== 'sell' || !in_array($params['charity'], $charityIds)))
            || (in_array($params['charity'], $charityIds) && ($params['listingType'] !== 'sell' || !isset($params['checkCharity'])))) {
            $arrayError[] = "Only the proceeds from sales can be donated to charity, make sure the confirmation & charity are selected!";
        }
        elseif($params['listingType'] === 'sell') {
            if(trim($params['inputPrice'] == '')) {
                $arrayError[] = 'Your sell amount is empty!';
            }
            elseif(!is_numeric(trim($params['inputPrice']))) {
                $arrayError[] = 'This is not a valid sell number!';
            }
            elseif(trim($params['inputPrice']) > 100000 || trim($params['inputPrice'] < 1)) {
                $arrayError[] = 'Your item is either too expensive or a negative value! (Min: £1 | Max: £100,000)';
            }
        }
        elseif($params['listingType'] === 'exchange') {
            if(trim($params['inputItem'] == '')) {
                $arrayError[] = 'Your exchange item is empty!';
            }
            elseif(strlen(trim($params['inputItem'])) > 50) {
                $arrayError[] = 'Your item name is too long! (Max characters: 50)';
            }
        }
        elseif($params['listingType'] === 'giveaway') {
            if (!isset($params['checkConf'])) {
                $arrayError[] ='You need to confirm your giveaway!';
            }
        }

        if(!empty($params['inputTags'])) {
            foreach($tags as $tag)
                if(!preg_match("/^[a-zA-Z0-9_\-]{1,20}$/", $tag) || !preg_match('/[A-Za-z0-9]/', $tag))
                {
                    $arrayError[] ='Only Alphanumeric / Dashes / Underscores are allowed in your tags! (Max Char per tag: 20)';
                    break;
                }
        }

        if(count($arrayError) > 0)
        {
            $view = View::render('/listings/create', ['errors' => $arrayError, 'params' => $params, 'charities' => $charities]);
        }

        else {
            $dataset->createListing(
                preg_replace('/\s+/', ' ', $params['inputTitle']),
                preg_replace('/\s+/', ' ',$params['description']),
                $params['listingType'] === 'sell' ? $params['inputPrice'] : null,
                $params['listingType'] === 'exchange' ? preg_replace('/\s+/', ' ',$params['inputItem']) : null,
                $params['listingType'],
                [],
                Auth::getAuthManager()->getUser()->getUserId(),
                $images,
                $params['charity']
            );
            $view = View::render('/listings/createSuccessful');
        }
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

    $app->post('/listings/{listingId:[0-9]+}/order', function (Request $request, Response $response, array $args) {
        $orderDataset = new OrderDataset();
        $result = $orderDataset->makeOrder($args['listingId']);
        if (is_string($result))
            return $response->withHeader('Location', "/listings/{$args['listingId']}")->withStatus(302);

        $view = View::render('listings/orderSuccess');
        $response->getBody()->write($view);
        return $response;
    });
};