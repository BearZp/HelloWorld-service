<?php

namespace App\Controller\Directory;

use App\Component\Directory\DirectoryComponentInterface;
use App\Controller\BaseController;
use App\Doctrine\Exception\NotFoundException;
use App\Request\Directory\ByDirectoryIdRequest;
use App\Request\RequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class GetListController extends BaseController
{
    /**
     * @var DirectoryComponentInterface
     */
    protected $component;

    /**
     * GetListController constructor.
     * @param DirectoryComponentInterface $component
     */
    public function __construct(DirectoryComponentInterface $component)
    {
        /** @var  Route $route */ //Only for sniffer!!!
        $this->component = $component;
    }

    /**
     * @Route("/directory/getList", name="getDirectoryList", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            $result = $this->component->getList();
        } catch (NotFoundException $e) {
            return $this->returnError(['Directories not found'], 400, $e);
        } catch (Throwable $e) {
            return $this->returnError([$e->getMessage()], 500, $e);
        }

        return $this->returnSuccess($result);
    }
}
