<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

use byteShard\Enum\Export\Action;
use byteShard\Environment;
use byteShard\Internal\Struct\ClientData;
use byteShard\Internal\Struct\GetData;
use byteShard\Locale;
use byteShard\Internal\ExportHandler;
use byteShard\Session;

$debug   = false;
$timeout = 600;
$setup   = false;
require __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config.php';

require_once BS_FILE_BOOTSTRAP_APP;

ini_set('memory_limit', '1536M');
ini_set('max_execution_time', (string)$timeout);
ini_set('max_input_time', (string)$timeout);

$component_name = Locale::get('byteShard.bs_export.default_filename');
$xid            = array_key_exists('xid', $_GET) ? $_GET['xid'] : '';
$eventId        = '';
if (array_key_exists('id', $_GET)) {
    $eventId = $_GET['id'];
}
$eventName = $_GET['ev'] ?? '';

$clientData = null;
if (array_key_exists('cd', $_GET) && !empty($_GET['cd'])) {
    try {
        $clientData = unserialize(Session::decrypt($_GET['cd']));
    } catch (Exception) {
    }
}
$getData = null;
if (array_key_exists('gd', $_GET) && !empty($_GET['gd'])) {
    try {
        $getData = unserialize(Session::decrypt($_GET['gd']));
    } catch (Exception) {
    }
}

$action = Action::tryFrom($_GET['action'] ?? '');
// used in toolbar exports... check if needed
$exportId = array_key_exists('exportId', $_GET) ? $_GET['exportId'] : '';

if (!($clientData === null || $clientData instanceof ClientData)) {
    exit;
}

if (!($getData === null || $getData instanceof GetData)) {
    exit;
}

if ($action === null) {
    exit;
}

if (isset($error_handler, $env) && ($env instanceof Environment)) {
    $exportHandler = new ExportHandler($error_handler, $xid, $eventId, $env->getAppName(), $exportId, $eventName, $clientData, $getData);
    $exportHandler->getExport($action);
}
