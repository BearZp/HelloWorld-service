<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: bearzp
 * Date: 22.10.20
 * Time: 13:18
 */

namespace App;

use Client\amqp\AmqpProtocol;
use Lib\protocol\ProtocolPacket;
use Lib\protocol\ProtocolPacketInterface;
use Symfony\Component\HttpFoundation\Request;
use Swoole\Http\Request as SwooleRequest;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ServiceKernel extends Kernel
{
    /** @var string */
    private $responseAmqpChanel;

    /** @var string */
    private $correlationId;

    /**
     * @param SwooleRequest $request
     * @return Request
     */
    public function transformRequest(SwooleRequest $request): Request {
        $method  = $request->server['request_method'];

        $post = $request->post ?? [];
        if (in_array(strtoupper($method), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            if (isset($request->header['Content-Type'])
                && (0 === strpos($request->header['Content-Type'], 'application/x-www-form-urlencoded'))
            ) {
                parse_str($request->getContent(), $post);
            }

            if (isset($request->header['Content-Type'] )
                && (0 === strpos($request->header['Content-Type'], 'application/json'))
            ) {
                $post = json_decode($request->getContent());
            }
        }
        if ($request->getContent()) {
            /** @var ProtocolPacketInterface $packet */
            parse_str($request->getContent(), $packet);
            if (isset($packet['props'])) {
                $this->correlationId = $packet['props']['correlation_id'];
                $this->responseAmqpChanel = $packet['props']['reply_to'];
            }

            $paket  = json_decode(gzuncompress($packet['packet']), true);
            if ($paket === null && json_last_error() !== JSON_ERROR_NONE) {
                $message = 'JSON decode error: ' . json_last_error();
                if (\function_exists('json_last_error_msg')) {
                    $message .= ' ' . json_last_error_msg();
                }
                throw new \Exception($message);
            }
            $packet = new ProtocolPacket(
                $packet['action'],
                $packet['data'],
                $packet['scope'],
                $packet['requestId']
            );
        } else {
            $packet = '';
        }

        $sfRequest = new Request(
            $request->get ?? [],
            $post,
            [],
            $request->cookie ?? [],
            $request->files ?? [],
            $request->server,
            $packet
        );

        $sfRequest->setMethod($method);
        $sfRequest->headers->replace($request->header);
        $sfRequest->server->set('REQUEST_URI', $request->server['request_uri']);

        if (isset($request->header['host'])) {
            $sfRequest->server->set('SERVER_NAME', $request->header['host']);
        }

        return $sfRequest;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, int $type = HttpKernelInterface::MASTER_REQUEST, bool $catch = true)
    {
        $sendResponse = false;
        if ($request->getContent()) {
            var_dump($request->getContent());
        }

        $response = parent::handle($request, $type, $catch);

        return $response;
    }
}