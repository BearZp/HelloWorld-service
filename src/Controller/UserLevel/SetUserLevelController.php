<?php

namespace App\Controller\UserLevel;

use App\Component\UserLevel\UserLevelComponent;
use App\Controller\BaseController;
use App\Request\RequestException;
use App\Request\UserLevel\SetUserLevelRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Throwable;
use Symfony\Component\Routing\Annotation\Route;

class SetUserLevelController extends BaseController
{
    /**
     * @var UserLevelComponent
     */
    protected $component;

    /**
     * GetUserLevelController constructor.
     * @param UserLevelComponent $component
     */
    public function __construct(UserLevelComponent $component)
    {
        /** @var  Route $route */ //Only for sniffer!!!
        $this->component = $component;
    }

    /**
     * @Route("/userLevel/setUserLevel", name="setUserLevel", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            $setUserLevelRequest = new SetUserLevelRequest($request);
        } catch (RequestException $e) {
            return $this->returnError($e->getErrors(), $e->getCode());
        }

        try {
            $result = $this->component->setUserLevel($setUserLevelRequest)->toArray();
        } catch (Throwable $e) {
            return $this->returnError([$e->getMessage()], 500, $e);
        }

        return $this->returnSuccess($result);
    }
}
