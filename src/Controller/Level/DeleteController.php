<?php

namespace App\Controller\Level;

use App\Component\level\LevelComponentInterface;
use App\Controller\BaseController;
use App\Request\Level\ByIdRequest;
use App\Request\RequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Tools\types\IntegerType;
use Throwable;
use InvalidArgumentException;

class DeleteController extends BaseController
{
    /** @var LevelComponentInterface */
    protected $component;

    public function __construct(LevelComponentInterface $component)
    {
        /** @var  Route $route */ //Only for sniffer!!!
        $this->component = $component;
    }

    /**
     * @Route("/level/deleteById", name="deleteLevelById", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            $request = new ByIdRequest($request);
        } catch (RequestException $e) {
            return $this->returnError($e->getErrors(), $e->getCode());
        }

        try {
            $this->component->deleteLevelById($request->getId());
        } catch (Throwable $e) {
            return $this->returnError([$e->getMessage()], 500, $e);
        }

        return $this->returnSuccess([]);
    }
}
