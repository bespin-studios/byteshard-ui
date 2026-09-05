<?php

use byteShard\Session;

require __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config.php';
$setup = false;

require_once BS_FILE_BOOTSTRAP_APP;

if (array_key_exists('action', $_REQUEST) && array_key_exists('itemId', $_REQUEST)) {
    $action = $_REQUEST['action'];
    $itemId = $_REQUEST['itemId'];
    try {
        $itemId = urldecode($itemId);
        $decrypted = Session::decrypt($itemId);
        $decoded   = json_decode($decrypted, true);
    } catch (\Exception $e) {
        $decoded = [];
        $filePath = 1;
    }
    switch ($action) {
        case 'loadImage':
            if (is_string($_REQUEST['itemValue']) && is_array($decoded)) {
                $decodedValue = json_decode($_REQUEST['itemValue'], true);
                if (is_array($decodedValue) && array_key_exists('i', $decoded) && array_key_exists($decoded['i'], $decodedValue)) {
                    $filePath = $decodedValue[$decoded['i']];
                    if (is_string($filePath)) {
                        $path = BS_FILE_PUBLIC_ROOT.'/'.ltrim($filePath, '/');
                        if (file_exists($path)) {
                            header('Content-Type: '.mime_content_type($path));
                            header('Cache-Control: private, no-cache, no-store, must-revalidate, pre-check=0, post-check=0, max-age=0, s-maxage=0');
                            header('Pragma: no-cache');
                            header('Expires: '.gmdate('r', 0));
                            echo file_get_contents($path);
                            exit;
                        }
                    }
                }
            }
            break;
        case 'uploadImage':
            break;
    }
}
