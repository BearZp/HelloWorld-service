<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: bearzp
 * Date: 22.10.20
 * Time: 13:18
 */

namespace App;

use Lib\protocol\ProtocolPacket;
use Lib\protocol\ProtocolPacketInterface;
use Symfony\Component\HttpFoundation\Request;
use Swoole\Http\Request as SwooleRequest;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ServiceKernel extends Kernel
{
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
            $packet = unserialize(gzuncompress(base64_decode($request->getContent())));
            $content = $packet;
        } else {
            $content = '';
        }

        $sfRequest = new Request(
            $request->get ?? [],
            $post,
            [],
            $request->cookie ?? [],
            $request->files ?? [],
            $request->server,
            $content
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
        $sendResponce = false;
        if ($request->getContent()) {
            /** @var ProtocolPacketInterface $packet */
            $packet = unserialize(gzuncompress(base64_decode($request->getContent())));
            $request->getContent()
            if($packet->getResponseChanel()) {

            }
        }



        $response = parent::handle($request, $type, $catch);

        return $response;
    }
}