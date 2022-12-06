<?php

use GroupThr3e\AJExchange\Util\View;

require_once '../vendor/autoload.php';

View::setPath('../src/Views');
echo View::render('index', ['name' => 'test']);