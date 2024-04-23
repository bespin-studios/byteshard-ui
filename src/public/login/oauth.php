<?php

use byteShard\Internal\Authentication\Authentication;
use byteShard\Internal\Authentication\Providers;
use byteShard\Internal\Server;
use byteShard\Environment;

require __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config.php';

$setup = true;

require_once BS_FILE_BOOTSTRAP_APP;

if (isset($env) && ($env instanceof Environment)) {
    $env->startSession();

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && array_key_exists('code', $_GET)) {
        $oauth         = new \byteShard\Internal\Authentication\Provider\Oauth($env->getOauthProvider(), $env->getJwksCertPath());
        $authenticated = $oauth->authenticate();
        if ($authenticated === true) {
            Authentication::setAuthenticationProviderCookie(Providers::OAUTH);
            $env->processSuccessfulLogin($oauth->getUsername());
            header('Location: '.Server::getBaseUrl());
            exit;
        }
    }
}
header('Location: '.Server::getBaseUrl().'/login');