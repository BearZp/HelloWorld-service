<?php

namespace App\Controller\Directory;

use App\Component\Directory\DirectoryComponentInterface;
use App\Controller\BaseController;
use App\Request\Directory\CreateDirectoryRequest;
use App\Request\RequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Throwable;
use Symfony\Component\Routing\Annotation\Route;

class CreateController extends BaseController
{
    /**@var DirectoryComponentInterface */
    protected $component;

    /**
     * CreateController constructor.
     * @param DirectoryComponentInterface $component
     */
    public function __construct(DirectoryComponentInterface $component)
    {
        /** @var Route $route */ //Only for sniffer!!!
        $this->component = $component;
    }

    /**
     * @Route("/directory/create", name="createDirectory", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            $createDirectoryRequest = new CreateDirectoryRequest($request);
        } catch (RequestException $e) {
            return $this->returnError($e->getErrors(), $e->getCode());
        }

        try {
            $result = $this->component->create($createDirectoryRequest)->toArray();
        } catch (Throwable $e) {
            return $this->returnError([$e->getMessage()], 500, $e);
        }

        return $this->returnSuccess($result);
    }
}
