<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

use byteShard\ID\ID;
use byteShard\Internal\HttpResponse;
use byteShard\Session;
use byteShard\Tab;
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
if ($requestData['action'] == 'getTabToolbar') {
    if ($requestData['tabID'] instanceof ID) {
        $tab = Session::getTab($requestData['tabID']);
    } else {
        $tab = null;
    }
    if ($tab instanceof Tab) {
        $className = $tab->getToolbarClass();
        if ($className !== '' && class_exists($className)) {
            $toolbarContent = new $className($tab);
            if ($toolbarContent instanceof Toolbar) {
                $array = $toolbarContent->getContents();
            }
        }
        $array['state'] = Enum\HttpResponseState::SUCCESS->value;
        $httpResponse->setResponseContent($array);
    }
}

$httpResponse->printHTTPResponse();
