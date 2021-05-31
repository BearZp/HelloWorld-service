<?php

namespace App\Controller\Directory;

use App\Component\Directory\DirectoryComponentInterface;
use App\Controller\BaseController;
use App\Doctrine\Exception\NotFoundException;
use App\Request\Directory\ByDirectoryNameRequest;
use App\Request\RequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class GetByNameController extends BaseController
{
    /**
     * @var DirectoryComponentInterface
     */
    protected $component;

    /**
     * GetByNameController constructor.
     * @param DirectoryComponentInterface $component
     */
    public function __construct(DirectoryComponentInterface $component)
    {
        /** @var  Route $route */ //Only for sniffer!!!
        $this->component = $component;
    }

    /**
     * @Route("/directory/getByName", name="getDirectoryByName", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            $request = new ByDirectoryNameRequest($request);
        } catch (RequestException $e) {
            return $this->returnError($e->getErrors(), $e->getCode());
        }

        try {
            $result = $this->component->getDirectoryByName($request->getName())->toArray();
        } catch (NotFoundException $e) {
            return $this->returnError(['Directory ' . $request->getName()->toString() . ' not found'], 400, $e);
        } catch (Throwable $e) {
            return $this->returnError([$e->getMessage()], 500, $e);
        }

        return $this->returnSuccess($result);
    }
}
