<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

use byteShard\Cell;
use byteShard\Enum;
use byteShard\Form;
use byteShard\Internal\CellContent;
use byteShard\Internal\ErrorHandler;
use byteShard\Internal\EventHandler;
use byteShard\Internal\HttpResponse;
use byteShard\Internal\Permission\Cell\NoPermission\NoPermissionCell;
use byteShard\Internal\Request;
use byteShard\Session;

/**
 * @var byteShard\Environment $env
 */

/*
 * this entry point is called every time a cell is loaded
 */

require __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config.php';
$setup = false;

require_once BS_FILE_BOOTSTRAP_APP;

if (isset($error_handler) && ($error_handler instanceof ErrorHandler)) {
    $error_handler->setResultObject(ErrorHandler::RESULT_OBJECT_CELL_CONTENT);
}

$request = new Request();

// get the whole cell including toolbar, events, parameters and so on
$response['state'] = 0;
if ($request->getEvent() === Request\EventType::OnCellInit || $request->getEvent() === Request\EventType::OnContainerInit) {

    // we need the current selected tab for various cell related implementation
    // only the parent tab is sent to the server when we select a tab in the client with nested tabs
    // because of that, we repeat the onTabSelect action for each cell to make sure that we have the correct nested tab
    $affectedId = $request->getAffectedId();
    if ($affectedId !== '') {
        $eventHandler = new EventHandler($env, $request);
        $eventHandler->onTabChange($request->getAffectedId());
    }

    $cell = Session::getCell($request->getId());
    if ($cell !== null) {
        $className   = $cell->getContentClass();
        $cellContent = new $className($cell);
        if ($cellContent instanceof CellContent) {
            if ($cellContent instanceof Form) {
                $cellContent->addFormSettings($env->getFormSettings());
            }
            $cellContent->setClientTimeZone($request->getClientTimeZone());
            $response          = $cellContent->getCellContent();
            $response['state'] = 2;
        } elseif ($cellContent instanceof \byteShard\Container) {
            $response          = $cellContent->getCellContent();
            $response['state'] = 2;
        }
    } else {
        $cell = new Cell();
        $cell->setAccessType(Enum\AccessType::RW);
        // TODO check if form package is loaded
        $cellContent       = new NoPermissionCell($cell);
        $response          = $cellContent->getCellContent();
        $response['state'] = 2;
    }
}

$httpResponse = new HttpResponse(Enum\HttpResponseType::JSON);
$httpResponse->setResponseContent($response);
$httpResponse->printHTTPResponse();
