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

$tab_id = $_POST['tabID'];
$cell_id = $_POST['cellID'];
$session = $_SESSION[MAIN];
/* @var $session byteShard\Internal\Session */

$id = ID::CellIdHelper($tab_id, $cell_id ?? '');

$cell = $session->getCell(ID::decrypt($id));
$controls = $cell->getContentControlType();
foreach ($_POST as $key => $val) {
    if (array_key_exists($key, $controls)) {
        $_POST[$controls[$key]['name']] = $val;
        unset($_POST[$key]);
    }
}
