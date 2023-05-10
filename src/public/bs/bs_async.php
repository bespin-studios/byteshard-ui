<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

file_put_contents('php://stderr', json_encode(['channel' => 'byteShard', 'context' => ['file' => __FILE__, 'line' => 13], 'extra' => [], 'datetime' => \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true)), new \DateTimeZone('UTC')), 'level' => 100, 'level_name' => 'DEBUG', 'message' => 'bs_async called'])."\n");

use byteShard\Internal\Debug;

$setup = false;
require __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config.php';

require_once BS_FILE_BOOTSTRAP_APP;

if (isset($_COOKIE['BS_ASYNC'], $_SESSION['async'], $_SESSION['async'][$_COOKIE['BS_ASYNC']], $_SESSION['async'][$_COOKIE['BS_ASYNC']]['action'])) {
    $result['state'] = 2;
    Debug::debug('[bs::async] call initiated');
    $actions         = $_SESSION['async'][$_COOKIE['BS_ASYNC']]['action']['nested'];
    $data            = $_SESSION['async'][$_COOKIE['BS_ASYNC']]['action']['id'];
    if (is_string($_SESSION['async'][$_COOKIE['BS_ASYNC']]['action']['cell'])) {
        $cell = $_SESSION[MAIN]->getCell($_SESSION[MAIN]->getIDByName($_SESSION['async'][$_COOKIE['BS_ASYNC']]['action']['cell']));
    } else {
        $cell = $_SESSION['async'][$_COOKIE['BS_ASYNC']]['action']['cell'];
    }
    unset($_SESSION['async'][$_COOKIE['BS_ASYNC']]);

    if (is_array($actions)) {
        $merge_array = [];
        foreach ($actions as $action) {
            /* @var \byteShard\Internal\Action $action */
            $merge_array[] = $action->getResult($cell, $data);
        }
        $result = array_merge_recursive($result, ...$merge_array);
    }
    if (is_array($result['state'])) {
        $result['state'] = min(2, min($result['state']));
    }
    Debug::debug('[bs::async] finished');
}
