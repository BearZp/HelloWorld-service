<?php

namespace App\Controller;

use App\Component\BaseComponentInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tools\logger\LogReferenceTrait;
use Throwable;

class BaseController extends AbstractController implements TokenAuthenticatedController
{
    use LogReferenceTrait;

    /** @var BaseComponentInterface */
    protected $component;

    /** @var string */
    protected $requestId;

    /** @var array */
    protected $scope;

    /**
     * @param string $requestId
     */
    public function setRequestId(string $requestId): void
    {
        $this->component->setRequestId($requestId);
        $this->requestId = $requestId;
    }

    /**
     * @param array $scope
     */
    public function setScope(array $scope): void
    {
        $this->component->setScope($scope);
        $this->scope = $scope;
    }

    /**
     * @param array $data
     * @return JsonResponse
     */
    public function returnSuccess(array $data): JsonResponse
    {
        return $this->json([
            'status' => 'ok',
            'data' => $data,
            'errors' => [],
            'scope' => $this->scope,
            'request_id' => $this->requestId
        ])->setStatusCode(200);
    }

    /**
     * @param array $errors
     * @param int $statusCode
     * @param Throwable|null $exception
     * @return JsonResponse
     */
    public function returnError(array $errors, int $statusCode = 500, Throwable $exception = null): JsonResponse
    {
        $dbt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = isset($dbt[1]['function']) ? $dbt[1]['function'] : null;
        $logMessage = 'Error while executing action: :actionName';
        $logContext = [
            'serviceName' => $this->getParameter('app.service.name'),
            'actionName' => $caller,
            'exception' => $exception,
            'requestId' => $this->requestId
        ];

        switch ($statusCode) {
            case $statusCode === 500 : {
                $this->getLogger()->critical($logMessage, $logContext);
                if ($this->getParameter('app.env') !== 'dev') {
                    $errors = ['Internal error'];
                }
            }
            break;
            case $statusCode === 400 : {
                $this->getLogger()->info($logMessage, $logContext);
            }
                break;
            default : {
                $this->getLogger()->error($logMessage, $logContext);
            }
            break;
        }

        return $this->json([
            'status' => 'error',
            'data' => [],
            'errors' => $errors,
            'scope' => $this->scope,
            'request_id' => $this->requestId
        ])->setStatusCode($statusCode);
    }
}
