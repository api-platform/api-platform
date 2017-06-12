<?php

use App\Kernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

require __DIR__.'/../vendor/autoload.php';

if (getenv('APP_DEBUG')) {
    // This check prevents access to debug front controllers that are deployed by accident to production servers.
    // Feel free to remove this, extend it, or make something more sophisticated.
    $whitelistedIps = ['127.0.0.1', '::1'];

    // Allow access from the host
    if ($dockerBridgeIp = getenv('DOCKER_BRIDGE_IP')) {
        $whitelistedIps[] = $dockerBridgeIp;
    }
    if (isset($_SERVER['HTTP_CLIENT_IP'])
        || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
        || !(in_array(@$_SERVER['REMOTE_ADDR'], $whitelistedIps) || php_sapi_name() === 'cli-server')
    ) {
        header('HTTP/1.0 403 Forbidden');
        exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
    }

    Debug::enable();
}

// Request::setTrustedProxies(['0.0.0.0/0'], Request::HEADER_FORWARDED);

$kernel = new Kernel(getenv('APP_ENV'), getenv('APP_DEBUG'));
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
