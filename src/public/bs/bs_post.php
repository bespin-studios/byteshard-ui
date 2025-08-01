<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */


/*
 * Include in any file to resolve encrypted control names
 */

use byteShard\ID\ID;

require __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config.php';
$setup = false;

require_once BS_FILE_BOOTSTRAP_APP;

$tabId   = is_string($_POST['tabID']) ? $_POST['tabID'] : throw new Exception('tabID is not a string');
$cellId  = is_string($_POST['cellID']) ? $_POST['cellID'] : throw new Exception('cellID is not a string');
$session = $_SESSION[MAIN];
if (!($session instanceof byteShard\Internal\Session)) {
    throw new Exception('Session is not a byteShard\Session');
}

$id = ID::CellIdHelper($tabId, $cellId);

$cell     = $session->getCell(ID::decrypt($id));
$controls = $cell?->getContentControlType();
foreach ($_POST as $key => $val) {
    if (is_array($controls) && array_key_exists($key, $controls) && is_array($controls[$key])) {
        $_POST[$controls[$key]['name']] = $val;
        unset($_POST[$key]);
    }
}
