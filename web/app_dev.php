<?php

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

// If you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#checking-symfony-application-configuration-and-setup
// for more information
//umask(0000);

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
$whiteListedAddresses = ['127.0.0.1', 'fe80::1', '::1'];
if (isset($_SERVER['DOCKER_BRIDGE_IP'])) {
    $whiteListedAddresses[] = $_SERVER['DOCKER_BRIDGE_IP'];
}

if ('varnish' !== $proxyIp = gethostbyname('varnish')) {
    $whiteListedAddresses[] = $proxyIp;
} else {
    $proxyIp = false;
}

if (isset($_SERVER['HTTP_CLIENT_IP'])
    || (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && ($proxyIp !== @$_SERVER['REMOTE_ADDR'] || !in_array($_SERVER['HTTP_X_FORWARDED_FOR'], $whiteListedAddresses)))
    || !(in_array(@$_SERVER['REMOTE_ADDR'], $whiteListedAddresses) || php_sapi_name() === 'cli-server')
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require __DIR__.'/../vendor/autoload.php';
Debug::enable();

if ($proxyIp) {
    Request::setTrustedProxies([$proxyIp], Request::HEADER_FORWARDED);
}

$kernel = new AppKernel('dev', true);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
