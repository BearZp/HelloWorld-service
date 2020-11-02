<?php
/**
 * Created by PhpStorm.
 * User: bearzp
 * Date: 18.09.20
 * Time: 15:50
 */


use App\ServiceKernel;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputArgument;

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

/* check console arguments */
$inArg = new ArgvInput($_SERVER['argv']);
if ( $inArg->getFirstArgument() === null ) {
    $green = "\033[0;32m";
    $lgreen = "\033[0;92m";
    $noColor = "\033[0;21m";
    echo PHP_EOL . 'This is the run script for demonized service v' . $_SERVER['APP_VERSION'] . PHP_EOL . PHP_EOL;
    echo $lgreen . 'Usage:' . $noColor. PHP_EOL;
    echo '  run.php 127.0.0.1 8080' . PHP_EOL . PHP_EOL;
    exit (1);
}
$inArg->bind(new InputDefinition([
    new InputArgument('host', InputArgument::REQUIRED),
    new InputArgument('port', InputArgument::REQUIRED),
]));
$host = $inArg->getArgument('host');
$port = (int) $inArg->getArgument('port');
/* ----------- */

/* init symfony kernel */
try {
    $kernel = new ServiceKernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
    $kernel->boot();
    $logger = $kernel->getContainer()->get('logger');
} catch (\Throwable $e) {
    $logger->error($e->getMessage(), [
        'exception' => $e,
        'serviceName' => $_SERVER['APP_SERVICE_NAME'],
        'actionName' => 'Run'
    ]);

    var_dump($e->getMessage());
    var_dump($e->getTraceAsString());
    exit;
}
/* ----------- */

/* Init Swoole server */
$server = new \Swoole\HTTP\Server($host, $port);
$server->on("start", function (\Swoole\Http\Server $server) use ($port) {
    echo "Swoole http server is started at http://127.0.0.1:$port\n";
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

    $response->end($sfResponse->getContent());

    $kernel->terminate($sfRequest, $sfResponse);
});

$count = 0;

$server->on('timer', function() use ($count) {
    try {
        $count++;
        if( $count % 10 == 0) {
            var_dump("TICK");
        }
    } catch(Exception $e) {
        //exception code
    }
}
);

$server->start();

var_dump('do something else');