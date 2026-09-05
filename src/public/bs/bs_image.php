<?php

use byteShard\Session;

require __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config.php';
$setup = false;

if (file_exists(BS_VENDOR_AUTOLOAD)) {
    require_once BS_VENDOR_AUTOLOAD;
} else {
    // TODO do something if autoloader does not exist
    file_put_contents('php://stderr', 'composer autoloader does not exist, please make sure that the file vendor/autoload.php exists');
    return;
}

if (array_key_exists('action', $_REQUEST) && $_REQUEST['action'] === 'loadImage' && array_key_exists('itemId', $_REQUEST) && array_key_exists('itemValue', $_REQUEST)) {
    $itemId = $_REQUEST['itemId'];
    try {
        if (!defined('MAIN')) {
            define('MAIN', 'byteShard');
        }
        // Session needed for decrypt
        Session::createSession('en', true);
        $decrypted    = Session::decrypt($itemId);
        $decoded      = json_decode($decrypted, true);
        $decodedValue = json_decode($_REQUEST['itemValue'], true);
        if (array_key_exists('i', $decoded) && array_key_exists($decoded['i'], $decodedValue)) {
            $filePath = $decodedValue[$decoded['i']];
            if (is_string($filePath)) {
                $path = BS_FILE_PUBLIC_ROOT.'/'.ltrim($filePath, '/');
                if (file_exists($path)) {
                    header('Content-Type: '.mime_content_type($path));
                    header('Cache-Control: private, no-cache, no-store, must-revalidate, pre-check=0, post-check=0, max-age=0, s-maxage=0');
                    header('Pragma: no-cache');
                    header('Expires: '.gmdate('r', 0));
                    echo file_get_contents($path);
                }
            }
        }
    } catch (\Exception $e) {
        $filePath = 1;
    }
}
