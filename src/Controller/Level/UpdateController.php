<?php

namespace App\Controller\Level;

use App\Component\Level\LevelComponentInterface;
use App\Controller\BaseController;
use App\Request\Level\UpdateLevelRequest;
use App\Request\RequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class UpdateController extends BaseController
{
    /** @var LevelComponentInterface */
    protected $component;

    public function __construct(LevelComponentInterface $component)
    {
        /** @var  Route $route */ //Only for sniffer!!!
        $this->component = $component;
    }

    /**
     * @Route("/level/update", name="updateLevel", methods={"POST"})
     *
     * @return JsonResponse
     * @throws
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            $updateLevelRequest = new UpdateLevelRequest($request);
        } catch (RequestException $e) {
            return $this->returnError($e->getErrors(), $e->getCode());
        }

        try {
            $result = $this->component->updateLevel($updateLevelRequest)->toArray();
        } catch (Throwable $e) {
            return $this->returnError([$e->getMessage()], 500, $e);
        }

        return $this->returnSuccess($result);
    }
}
