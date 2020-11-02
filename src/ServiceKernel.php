<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: bearzp
 * Date: 22.10.20
 * Time: 13:18
 */

namespace App;

use Symfony\Component\HttpFoundation\Request;
use Swoole\Http\Request as SwooleRequest;

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

        $sfRequest = new Request(
            $request->get ?? [],
            $post,
            [],
            $request->cookie ?? [],
            $request->files ?? [],
            $request->server,
            $request->getContent()
        );

        $sfRequest->setMethod($method);
        $sfRequest->headers->replace($request->header);
        $sfRequest->server->set('REQUEST_URI', $request->server['request_uri']);

        if (isset($request->header['host'])) {
            $sfRequest->server->set('SERVER_NAME', $request->header['host']);
        }

        return $sfRequest;
    }
}