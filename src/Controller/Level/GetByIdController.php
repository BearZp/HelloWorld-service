<?php

namespace App\Controller\Level;

use App\Component\Level\LevelComponentInterface;
use App\Controller\BaseController;
use App\Doctrine\Exception\NotFoundException;
use App\Request\Level\ByIdRequest;
use App\Request\RequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;;
use Throwable;

class GetByIdController extends BaseController
{
    /** @var LevelComponentInterface */
    protected $component;

    public function __construct(LevelComponentInterface $component)
    {
        /** @var  Route $route */ //Only for sniffer!!!
        $this->component = $component;
    }

    /**
     * @Route("/level/getById", name="getLevelById", methods={"POST"})
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
            $result = $this->component->getLevelById($request->getId())->toArray();
        } catch (NotFoundException $e) {
            return $this->returnError(['Level with ID ' . $request->getId()->toInteger() . ' not found'], 404, $e);
        } catch (Throwable $e) {
            return $this->returnError([$e->getMessage()], 500, $e);
        }

        return $this->returnSuccess($result);
    }
}
