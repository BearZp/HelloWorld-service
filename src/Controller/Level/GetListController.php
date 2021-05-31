<?php

namespace App\Controller\Level;

use App\Component\Level\LevelComponentInterface;
use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class GetListController extends BaseController
{
    /** @var LevelComponentInterface */
    protected $component;

    public function __construct(LevelComponentInterface $component)
    {
        /** @var  Route $route */ //Only for sniffer!!!
        $this->component = $component;
    }

    /**
     * @Route("/level/getList", name="getLevelsList", methods={"POST"})
     *
     * @return JsonResponse
     * @throws
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            $result = $this->component->getLevelsList();
        } catch (Throwable $e) {
            return $this->returnError([$e->getMessage()], 500, $e);
        }

        return $this->returnSuccess($result);
    }
}
