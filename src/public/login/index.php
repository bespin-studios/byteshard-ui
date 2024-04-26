<?php

use byteShard\Environment;
use byteShard\Internal\ErrorHandler;
use byteShard\Internal\Server;

require __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config.php';

$setup = true;

require_once BS_FILE_BOOTSTRAP_APP;
if (isset($error_handler) && $error_handler instanceof ErrorHandler) {
    $error_handler->setResultObject(ErrorHandler::RESULT_OBJECT_LOGIN);
}

if (isset($env) && $env instanceof Environment) {
    $env->getLoginTemplate()->printLoginForm(Server::getBaseUrl(), $env->getAppName(), $env->getFaviconPath());
}
