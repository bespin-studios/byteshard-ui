<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

use byteShard\Internal\HttpResponse;
use byteShard\Internal\Session;
use byteShard\Toolbar;
use byteShard\Enum;

/*
 * this entry point is called every time a tab is loaded
 */

require __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config.php';
$setup = false;

require_once BS_FILE_BOOTSTRAP_APP;

$httpResponse = new HttpResponse(Enum\HttpResponseType::JSON);

$jsonClientData = file_get_contents('php://input');
if (!is_string($jsonClientData)) {
    $httpResponse->printHTTPResponse();
}

$requestData = json_decode($jsonClientData, true);
if (!is_array($requestData)) {
    $httpResponse->printHTTPResponse();
}

// get the Toolbar of a Tab
if ($requestData['action'] == 'getTabToolbar' && $_SESSION[MAIN] instanceof Session) {
    $tab = $_SESSION[MAIN]->getTab($requestData['tabID']);
    if ($tab !== null) {
        $className = $tab->getToolbarClass();
        if ($className !== '' && class_exists($className)) {
            $toolbarContent = new $className($tab);
            if ($toolbarContent instanceof Toolbar) {
                $array = $toolbarContent->getContents();
            }
        }
        $array['state'] = 2;
        $httpResponse->setResponseContent($array);
    }
}

$httpResponse->printHTTPResponse();
