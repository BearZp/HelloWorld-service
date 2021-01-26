<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: bearzp
 * Date: 22.10.20
 * Time: 13:18
 */

namespace App;


use Lib\logger\LogReferenceTrait;
use Lib\protocol\AmqpProtocol;
use Lib\protocol\ProtocolInterface;
use Lib\protocol\ProtocolPacket;
use Lib\protocol\ProtocolPacketInterface;
use Lib\queues\rabbitMq\LazyRabbitMqConnectionProvider;
use Lib\types\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Amp\Http\Server\Request as AmpRequest;
use Amp\Http\Server\Response as AmpResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class AmpServiceKernel extends Kernel
{
    use LogReferenceTrait;

    /** @var string */
    private $responseAmqpChanel;

    /** @var string */
    private $correlationId;

    /** @var ProtocolInterface */
    private $activeProtocol;

    /** @var ProtocolPacketInterface */
    private $incomePaket;

    /**
     * @param AmpRequest $request
     * @param string $content
     * @return Request
     * @throws \Exception
     */
    public function transformRequest(AmpRequest $request, string $content): Request {
        $method  = $request->getMethod();

        // base decode post data

        $post = [];
        if (in_array(strtoupper($method), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            if (0 === strpos($request->getHeader('Content-Type'), 'application/x-www-form-urlencoded')
            ) {
                parse_str($content, $post);
            }

            if (0 === strpos($request->getHeader('Content-Type'), 'application/json')
            ) {
                $post = json_decode($content, true);
            }
        }

        //detect protocol
        if (isset($post['props']) && $post['props'] !== 'false') {
            /** @var LazyRabbitMqConnectionProvider $connectionProvider */
            $connectionProvider = $this->getContainer()->get('lazyAmqpConnection');
            $this->correlationId =      $post['props']['correlation_id'];
            $this->responseAmqpChanel = $post['props']['reply_to'];
            $this->activeProtocol = new AmqpProtocol(
                $connectionProvider,
                $this->responseAmqpChanel,
                false,
                $this->getContainer()->getParameter('clientConnectionTimeout')
            );
            $this->incomePaket = $this->activeProtocol->catchPacket($post['packet']);
        } else {
            $this->incomePaket = new ProtocolPacket(
                $uri = $request->getUri()->getPath(),
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
            $_SERVER,
            $post
        );

        $sfRequest->setMethod($method);
        $sfRequest->headers->replace($request->getHeaders());
        $sfRequest->server->set('REQUEST_URI', $this->incomePaket->getAction());

        if (isset($request->header['host'])) {
            $sfRequest->server->set('SERVER_NAME', $request->getHeader('host'));
        }

        return $sfRequest;
    }

    /**
     * @param Response $sfResponse
     * @param AmpResponse $response
     * @throws \Throwable
     */
    public function transformResponse(Response $sfResponse, AmpResponse $response): void
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
            $response->setStatus($sfResponse->getStatusCode());
            $response->setHeaders($sfResponse->headers->all());
            $response->setBody($sfResponse->getContent());
        }
    }
}