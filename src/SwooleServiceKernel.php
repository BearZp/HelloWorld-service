<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: bearzp
 * Date: 22.10.20
 * Time: 13:18
 */

namespace App;


use Lib\protocol\AmqpProtocol;
use Lib\protocol\ProtocolInterface;
use Lib\protocol\ProtocolPacket;
use Lib\protocol\ProtocolPacketInterface;
use Lib\types\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class SwooleServiceKernel extends Kernel
{
    /** @var string */
    private $responseAmqpChanel;

    /** @var string */
    private $correlationId;

    /** @var ProtocolInterface */
    private $activeProtocol;

    /** @var ProtocolPacketInterface */
    private $incomePaket;

    /**
     * @param SwooleRequest $request
     * @return Request
     */
    public function transformRequest(SwooleRequest $request): Request {
        $method  = $request->server['request_method'];

        // base decode post data
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

        //detect protocol
        if (isset($post['props']) && $post['props'] !== 'false') {
            $this->correlationId =      $post['props']['correlation_id'];
            $this->responseAmqpChanel = $post['props']['reply_to'];
            $this->activeProtocol = new AmqpProtocol(
                $this->getContainer()->get('lazyAmqpConnection'),
                $this->responseAmqpChanel,
                false,
                $this->getContainer()->getParameter('clientConnectionTimeout')
            );
            $this->incomePaket = $this->activeProtocol->catchPacket($post['packet']);
        } else {
            $this->incomePaket = new ProtocolPacket(
                $request->server['request_uri'],
                $post['data'],
                $post['scope'],
                $post['requestId']
            );
        }

        /// create Symfony request
        $sfRequest = new Request(
            $request->get ?? [],
            $this->incomePaket->getData(),
            [],
            $request->cookie ?? [],
            $request->files ?? [],
            $request->server,
            $post
        );

        $sfRequest->setMethod($method);
        $sfRequest->headers->replace($request->header);
        $sfRequest->server->set('REQUEST_URI', $this->incomePaket->getAction());

        if (isset($request->header['host'])) {
            $sfRequest->server->set('SERVER_NAME', $request->header['host']);
        }

        return $sfRequest;
    }

    /**
     * @param Response $sfResponse
     * @param SwooleResponse $response
     * @throws \Exception
     */
    public function transformResponse(Response $sfResponse, SwooleResponse $response)
    {
        if ($this->activeProtocol instanceof AmqpProtocol) {
            $paket = new ProtocolPacket(
                'Answer',
                json_decode($sfResponse->getContent(), true),
                $this->incomePaket->getScope(),
                $this->incomePaket->getRequestId()
            );
            $this->activeProtocol->pushPacket($paket, $this->correlationId);
        } else {

            $response->write($sfResponse->getContent());

        }
    }
}