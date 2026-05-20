<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

use byteShard\Cell;
use byteShard\Container;
use byteShard\DynamicCellContent;
use byteShard\Enum;
use byteShard\Form;
use byteShard\Internal\CellContent;
use byteShard\Internal\ContentClassFactory;
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
$response['state'] = Enum\HttpResponseState::ERROR->value;
if ($request->getEvent() === Request\EventType::OnCellInit || $request->getEvent() === Request\EventType::OnContainerInit) {

    // We need the current selected tab for various cell-related implementations.
    // Only the parent tab is sent to the server when we select a tab in the client with nested tabs.
    // Because of that, we repeat the onTabSelect action for each cell to make sure that we have the correct nested tab.
    $affectedId = $request->getAffectedId();
    if ($affectedId !== '') {
        $eventHandler = new EventHandler($env, $request);
        $eventHandler->onTabChange($request->getAffectedId());
    }

    $cell      = Session::getCell($request->getId());
    $className = '';
    $id        = $request->getId();
    if ($id?->isCellId() === true && $cell !== null) {
        $dynamicClassName = Cell::getClassName($id);
        if (class_exists($dynamicClassName) && is_subclass_of($dynamicClassName, DynamicCellContent::class)) {
            $dynamicClass = ContentClassFactory::cellContent($dynamicClassName, $request->getContext(), $cell);
            if ($dynamicClass instanceof DynamicCellContent) {
                $className    = $dynamicClass->getDynamicContentClassName();
                $cell         = $dynamicClass->getDynamicCell($className);
            }
        }
    }

    if ($cell !== null) {
        if ($className === '') {
            $className = $cell->getContentClass();
        }
        $cellContent = ContentClassFactory::cellContent($className, $request->getContext(), $cell);
        if ($cellContent instanceof CellContent) {
            if ($cellContent instanceof Form) {
                $cellContent->addFormSettings($env->getFormSettings());
            }
            $cellContent->setClientTimeZone($request->getClientTimeZone());
            $response          = $cellContent->getCellContent();
            $response?->setState(Enum\HttpResponseState::SUCCESS);
        } elseif ($cellContent instanceof Container) {
            $response          = $cellContent->getCellContent();
            $response?->setState(Enum\HttpResponseState::SUCCESS);
        }
    } else {
        $cell = new Cell();
        $cell->setAccessType(Enum\AccessType::RW);
        // TODO check if form package is loaded
        $cellContent       = new NoPermissionCell($cell, '');
        $response          = $cellContent->getCellContent();
        $response?->setState(Enum\HttpResponseState::SUCCESS);
    }
}

$httpResponse = new HttpResponse(Enum\HttpResponseType::JSON);
$httpResponse->setResponseContent($response);
$httpResponse->printHTTPResponse();
