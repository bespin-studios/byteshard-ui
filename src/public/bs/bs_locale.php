<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

use byteShard\Internal\HttpResponse;
use byteShard\Internal\ErrorHandler;
use byteShard\Enum\HttpResponseType;
use byteShard\Session;

/*
 * receives all locale changes of the locale.js and processes the changes
 */

$setup = false;

require __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config.php';

require_once BS_FILE_BOOTSTRAP_APP;

if (isset($error_handler) && ($error_handler instanceof byteShard\Internal\ErrorHandler)) {
    $error_handler->setResultObject(ErrorHandler::RESULT_OBJECT_POPUP);
}

$httpResponse = new HttpResponse(HttpResponseType::JSON);

$jsonClientData = file_get_contents('php://input');
if (!is_string($jsonClientData)) {
    $httpResponse->printHTTPResponse();
}

$requestData = json_decode($jsonClientData, true);
if (!is_array($requestData)) {
    $httpResponse->printHTTPResponse();
}

if (array_key_exists('action', $requestData) && array_key_exists('locale', $requestData) && $requestData['action'] === 'changeLocale') {
    $httpResponse->setResponseContent(Session::getLocaleForAllObjects($requestData['locale']));
} else {
    $httpResponse->setResponseContent(['state' => DEBUG === true ? 1 : 0]);
}

$httpResponse->printHTTPResponse();
