<?php
/**
 * Created by PhpStorm.
 * User: bearzp
 * Date: 18.09.20
 * Time: 15:50
 */

declare(strict_types=1);

use Amp\Http\Server\RequestHandler\CallableRequestHandler;
use Amp\Http\Server\HttpServer;
use Amp\Http\Server\Request as AmpRequest;
use Amp\Http\Server\Response as AmpResponse;
use Amp\Socket\Server;
use App\AmpServiceKernel;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Tools\cache\LocaleCache;
use function Amp\ByteStream\getStdout;

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
    $kernel = new AmpServiceKernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
    $kernel->boot();
    /** @var \Psr\Log\LoggerInterface $logger */
    $logger = $kernel->getContainer()->get('logger');
    $kernel->setLogger($logger);
} catch (Throwable $e) {
    var_dump($e->getMessage());
    var_dump($e->getTraceAsString());
    exit;
}
/* ----------- */

/* Init Amp Http server */
$logHandler = new StreamHandler(getStdout());
$logHandler->setFormatter(new ConsoleFormatter);
$logHandler->pushProcessor(new PsrLogMessageProcessor);

$logger = new Logger('server');
$logger->pushHandler($logHandler);

Amp\Loop::run(function () use ($kernel, $logger) {
    $sockets = [
        Server::listen($_ENV['WORKER_HOST'] . ':' . $_ENV['WORKER_PORT']),
        Server::listen($_ENV['WORKER_HOST_V6'] . ':' . $_ENV['WORKER_PORT']),
    ];

    $server = new HttpServer($sockets, new CallableRequestHandler(function (AmpRequest $request) use ($kernel) {
        $body = yield $request->getBody()->buffer();
        $sfRequest = $kernel->transformRequest($request, $body);
        try {
            ob_start();
            $sfResponse = $kernel->handle($sfRequest);
            $dump = ob_get_clean();
            if ($dump !== '') {
                $sfResponse->setContent($dump . $sfResponse->getContent());
            }
        } catch (Throwable $e) {
            $kernel->getLogger()->error('Internal server error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            $sfResponse = new Response('Internal server error', 500);
        }
        $response = new AmpResponse();
        $kernel->transformResponse($sfResponse, $response);

        return $response;
    }), $logger);

    Amp\Loop::repeat(5000, function () use ($kernel) {
        //clear cache
        /** @var LocaleCache $localCache */
        $localeCache = $kernel->getContainer()->get('cache.local');
        $localeCache->clearExpiredItems();
    });

    yield $server->start();

    // Stop the server gracefully when SIGINT is received.
    // This is technically optional, but it is best to call Server::stop().
    Amp\Loop::onSignal(SIGINT, function (string $watcherId) use ($server) {
        Amp\Loop::cancel($watcherId);
        yield $server->stop();
    });

    $logger->info('Service secret: ' . $_ENV['APP_SECRET']);
});
