<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

file_put_contents('php://stderr', json_encode(['channel' => 'byteShard', 'context' => ['file' => __FILE__, 'line' => 13], 'extra' => [], 'datetime' => DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true)), new DateTimeZone('UTC')), 'level' => 100, 'level_name' => 'DEBUG', 'message' => 'bs_async called'])."\n");

use byteShard\Enum\HttpResponseState;
use byteShard\Internal\Action;
use byteShard\Internal\Debug;

$setup = false;
require __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config.php';

require_once BS_FILE_BOOTSTRAP_APP;

$asyncKey = (string)(array_key_exists('BS_ASYNC', $_COOKIE) ? $_COOKIE['BS_ASYNC'] : '');

if (array_key_exists('async', $_SESSION)) {
    $async = $_SESSION['async'];
    if (is_array($async) && array_key_exists($asyncKey, $async)) {
        $asyncArray = $async[$asyncKey];
        if (is_array($asyncArray) && array_key_exists('action', $asyncArray)) {
            $actionPayload = $asyncArray['action'];
            if (is_array($actionPayload) && array_key_exists('nested', $actionPayload) &&  array_key_exists('id', $actionPayload) && array_key_exists('cell', $actionPayload)) {
                $result['state'] = HttpResponseState::SUCCESS->value;
                Debug::debug('[bs::async] call initiated');
                $actions = $actionPayload['nested'];
                $data    = $actionPayload['id'];
                //TODO: fix eventually. No idea why we need the local $cell var and no idea what's in actionPayload['cell']
                /*if (is_string($actionPayload['cell'])) {
                    $cell = $_SESSION[MAIN]->getCell($_SESSION[MAIN]->getIDByName($actionPayload['cell']));
                } else {
                    $cell = $actionPayload['cell'];
                }
                unset($_SESSION['async'][$asyncKey]);*/

                if (is_array($actions)) {
                    $merge_array = [];
                    foreach ($actions as $action) {
                        if ($action instanceof Action) {
                            //TODO: actionInit
                            $merge_array[] = $action->getResult();
                        }
                    }
                    $result = array_merge_recursive($result, ...$merge_array);
                }
                if (is_array($result['state'])) {
                    $result['state'] = min(2, $result['state']);
                }
                Debug::debug('[bs::async] finished');
            }
        }
    }
}
