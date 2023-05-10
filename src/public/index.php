<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

$setup = false;

require_once './config.php';

require_once BS_FILE_BOOTSTRAP_APP;

use byteShard\Environment;

if (isset($env) && ($env instanceof Environment)) {
    $env->printSiteBaseContainer();
}