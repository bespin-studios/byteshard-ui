<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

use byteShard\Enum;
use byteShard\Internal\HttpResponse;
use byteShard\Internal\Request;
use byteShard\Internal\Request\EventType;
use byteShard\Session;

/*
 * this entry point is called initially and returns the generic layout of all tabs
 */

$setup = false;
require __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config.php';
require_once BS_FILE_BOOTSTRAP_APP;

$request      = new Request();
$httpResponse = new HttpResponse(Enum\HttpResponseType::JSON);

if (isset($env) && $request->getEvent() === EventType::OnReady) {
    $response = Session::getNavigationArray($env->getDebug(), $env->getDhtmlxCssImagePath());
    $httpResponse->setResponseContent($response);
}

$httpResponse->printHTTPResponse();
