<?php

use App\Kernel;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;

require __DIR__.'/../vendor/autoload.php';

if ($_SERVER['APP_DEBUG'] ?? false) {
    // WARNING: You should setup permissions the proper way!
    // REMOVE the following PHP line and read
    // https://symfony.com/doc/current/book/installation.html#checking-symfony-application-configuration-and-setup
    umask(0000);

    Debug::enable();
}

if ($trustedProxies = $_SERVER['APP_TRUSTED_PROXIES'] ?? false) {
    Request::setTrustedProxies(json_decode($trustedProxies, true), Request::HEADER_X_FORWARDED_ALL);
}

if ($trustedHosts = $_SERVER['APP_TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts(json_decode($trustedHosts, true));
}

$kernel = new Kernel($_SERVER['APP_ENV'] ?? 'dev', $_SERVER['APP_DEBUG'] ?? false);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
