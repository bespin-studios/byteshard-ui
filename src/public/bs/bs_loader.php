<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

use byteShard\Internal\Action;
use byteShard\Internal\Debug;
use byteShard\Internal\ErrorHandler;
use byteShard\Internal\HttpResponse;
use byteShard\Enum;
use byteShard\ID;
use byteShard\Internal\Server;

$setup = false;
require __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config.php';

require_once BS_FILE_BOOTSTRAP_APP;

if (isset($error_handler) && ($error_handler instanceof byteShard\Internal\ErrorHandler)) {
    $error_handler->setResultObject(ErrorHandler::RESULT_OBJECT_POPUP);
}

if (
    isset($_SESSION['loaderState']) &&
    is_array($_SESSION['loaderState']) &&
    isset($_SESSION['loaderState']['action']) &&
    is_array($_SESSION['loaderState']['action'])
) {
    $result['state'] = Enum\HttpResponseState::SUCCESS->value;
    $actionArr       = $_SESSION['loaderState']['action'];
    $actions         = $actionArr['nested'] ?? null;
    $data            = $actionArr['id'] ?? null;
    $async           = $actionArr['async'] ?? null;
    $asyncUrl        = $actionArr['asyncUrl'] ?? null;
    $asyncActions    = $actionArr['asyncNested'] ?? null;
    $asyncProxy      = $actionArr['asyncProxy'] ?? null;
    $asyncTimeout    = $actionArr['asyncTimeout'] ?? null;

    //TODO: reimplement eventually
    /*$cell = isset($actionArr['cell']) && is_string($actionArr['cell'])
        ? $_SESSION[MAIN]->getCell($_SESSION[MAIN]->getIDByName($actionArr['cell']))
        : ($actionArr['cell'] ?? null);*/
    unset($_SESSION['loaderState']['action']);

    /*if ($async === true) {
        //exec('/usr/bin/php /Users/Shared/Sites/cam/public/log.php > /dev/null 2>/dev/null &');

        $id                                         = ID::UUID();
        $_SESSION['async'][$id]['action']['nested'] = $asyncActions;
        $_SESSION['async'][$id]['action']['cell']   = (string)$cell;
        $_SESSION['async'][$id]['action']['id']     = $data;

        $ch = curl_init();
        if (empty($asyncUrl)) {
            $host = explode(':', Server::getHost());
            if (count($host) === 2 && is_numeric($host[1])) {
                Debug::debug('[bs::loader] Async port: '.$host[1]);
                curl_setopt($ch, CURLOPT_PORT, (int)$host[1]);
            }
            $context  = str_replace(Server::getProtocol().'://'.Server::getHost(), '', BS_WEB_FRAMEWORK_DIR);
            $asyncUrl = rtrim(Server::getProtocol().'://'.$host[0].'/'.trim($context, '/'), '/').'/bs_async.php';
        }
        Debug::debug('[bs::loader] Async URL: '.$asyncUrl);
        curl_setopt($ch, CURLOPT_URL, $asyncUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, $asyncTimeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $asyncTimeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        if ($asyncProxy === false) {
            curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, false);
            curl_setopt($ch, CURLOPT_PROXY, '');
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Cookie: PHPSESSID='.session_id().'; BS_ASYNC='.$id]);
        $curl_response = curl_exec($ch);
        $curl_error    = curl_error($ch);
        curl_close($ch);
        if (!empty($curl_error)) {
            Debug::error('[bs::loader] Async error: '.$curl_error);
        }
    }*/
    if (is_array($actions)) {
        $merge_array = array();
        foreach ($actions as $action) {
            if ($action instanceof Action) {
                //TODO: actionInitDTO
                $merge_array[] = $action->getResult();
            }
        }
        $result = array_merge_recursive($result, ...$merge_array);
    }
    if (is_array($result['state'])) {
        $result['state'] = min(2, $result['state']);
    }
    $http_response = new HttpResponse(Enum\HttpResponseType::JSON);
    $http_response->setResponseContent($result);
    $http_response->printHTTPResponse();
}
