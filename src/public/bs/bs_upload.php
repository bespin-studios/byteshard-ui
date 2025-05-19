<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

/**
 * @var ErrorHandler $error_handler
 */

use byteShard\Exception;
use byteShard\Internal\HttpResponse;
use byteShard\Internal\ErrorHandler;
use byteShard\Enum;
use byteShard\Internal\Upload;

$setup = false;

require __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config.php';

require_once BS_FILE_BOOTSTRAP_APP;

$error_handler->setResultObject(ErrorHandler::RESULT_OBJECT_POPUP);
$httpResponse = new HttpResponse(Enum\HttpResponseType::JSON);

$mode = Enum\UploadMode::tryFrom(is_string($_REQUEST['mode']) ? $_REQUEST['mode'] : '');
if ($mode === null) {
    if (
        isset($_FILES['file']) &&
        is_array($_FILES['file']) &&
        !empty($_FILES['file']['tmp_name']) &&
        is_string($_FILES['file']['tmp_name'])
    ) {
        //error occurred, remove the uploaded file from the temp dir
        unlink($_FILES['file']['tmp_name']);
    }
    $exception = new Exception('Unsupported upload type', 0);
    $exception->setLocaleToken('byteShard.upload.type.unsupported');
    throw $exception;
}

$upload = new Upload();
$type   = isset($_GET['type']) && is_string($_GET['type']) ? $_GET['type'] : null;
$file   = isset($_FILES['file']) && is_array($_FILES['file']) ? $_FILES['file'] : null;
$result = $upload->getClientResult($type, $file);


$httpResponse->setResponseContent($result);
$httpResponse->printHTTPResponse();
