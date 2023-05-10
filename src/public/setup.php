<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

use byteShard\Environment;
use byteShard\Internal\Setup;

require __DIR__.DIRECTORY_SEPARATOR.'config.php';

$setup = true;

require_once BS_FILE_BOOTSTRAP_APP;

if (isset($env) && ($env instanceof Environment)) {
    $setup = new Setup($env);
    $setup->showForm();
}
