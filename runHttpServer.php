<?php
/**
 * Created by PhpStorm.
 * User: bearzp
 * Date: 18.09.20
 * Time: 15:50
 */

declare(strict_types=1);

use App\ServiceKernel;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require dirname(__FILE__).'/vendor/autoload.php';

/* collect env variables */
(new Dotenv())->bootEnv(dirname(__FILE__).'/.env');
/* ----------- */

/* Enable debug mod */
if ($_SERVER['APP_DEBUG']) {
    umask(0000);
    Debug::enable();
}
/* ----------- */

/* Modify base Request class */
if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? false) {
    Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST);
}

if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts([$trustedHosts]);
}
/* ----------- */

/* init symfony kernel */
try {
    $kernel = new ServiceKernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
    $kernel->boot();
    $logger = $kernel->getContainer()->get('logger');
} catch (\Throwable $e) {
    var_dump($e->getMessage());
    var_dump($e->getTraceAsString());
    exit;
}
/* ----------- */

/* Init Swoole server */
$server = new \Swoole\HTTP\Server($_ENV['WORKER_HOST'], (int) $_ENV['WORKER_PORT']);
$server->on("start", function (\Swoole\Http\Server $server) {
    echo "Swoole http server is started at http://" . $_ENV['WORKER_HOST'] . ":" . $_ENV['WORKER_PORT'] . "\n";
});
/* ----------- */

$server->on("request", function (\Swoole\Http\Request $request, \Swoole\Http\Response $response) use ($kernel, $logger) {

    $sfRequest = $kernel->transformRequest($request);

    try {
        $sfResponse = $kernel->handle($sfRequest);
    } catch (\Throwable $e) {
        $logger->error('Internal server error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        $sfResponse = new \Symfony\Component\HttpFoundation\Response('Internal server error', 500);
    }

    foreach ($sfResponse->headers as $key => $header) {
        $response->header($key, current($header));
    }

    if ($sfResponse->headers->get('Connection') === 'close') {
        $response->end();
    } else {
        $response->end($sfResponse->getContent());
    }

    $kernel->terminate($sfRequest, $sfResponse);
});

$server->start();