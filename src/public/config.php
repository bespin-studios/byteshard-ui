<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */


/**
 * the path where the byteShard and application directories reside
 * adjust in case the private file path is not the parent directory of public
 */
$private_file_path = null;

/**
 * DO NOT MODIFY
 */
if ($private_file_path === null) {
    $private_file_path = dirname(__DIR__);
}

if (substr($_SERVER['SCRIPT_FILENAME'], 0, 1) === '/') {
    // probably linux/unix based OS, paths start with /
    define('BS_FILE_PUBLIC_ROOT', DIRECTORY_SEPARATOR . trim(__DIR__, DIRECTORY_SEPARATOR));
} else {
    // probably windows based OS, paths start with drive letter, e.g. C:
    define('BS_FILE_PUBLIC_ROOT', trim(__DIR__, DIRECTORY_SEPARATOR));
}

define('BS_FILE_PRIVATE_ROOT', rtrim($private_file_path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
define(
    'BS_FILE_BOOTSTRAP_APP',
    BS_FILE_PRIVATE_ROOT . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'app.php'
);
define(
    'BS_VENDOR_AUTOLOAD',
    BS_FILE_PRIVATE_ROOT . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php'
);
