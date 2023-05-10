<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

use byteShard\Environment;
use byteShard\Internal\HttpResponse;
use byteShard\Internal\ErrorHandler;
use byteShard\Internal\EventHandler;
use byteShard\Internal\Request;
use byteShard\Enum;

/**
 * @var Environment $env
 */

/**
 * This is the endpoint for all javascript events from eventResult.class.js
 */

require __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config.php';
$setup = false;

require_once BS_FILE_BOOTSTRAP_APP;

if (isset($error_handler)) {
    $error_handler->setResultObject(ErrorHandler::RESULT_OBJECT_POPUP);
}

$request      = new Request();
$eventHandler = new EventHandler($env, $request);
$httpResponse = new HttpResponse(Enum\HttpResponseType::JSON);
$httpResponse->setResponseContent($eventHandler->getEventResult());

unset($eventHandler);
$httpResponse->printHTTPResponse();
