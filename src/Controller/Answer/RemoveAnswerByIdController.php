<?php

namespace App\Controller\Answer;

use App\Component\Answer\AnswerComponentInterface;
use App\Controller\BaseController;
use App\Doctrine\Exception\NotFoundException;
use App\Request\Answer\ByAnswerIdRequest;
use App\Request\RequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class RemoveAnswerByIdController extends BaseController
{
    /**
     * @var AnswerComponentInterface
     */
    protected $component;

    /**
     * RemoveAnswerByIdController constructor.
     * @param AnswerComponentInterface $component
     */
    public function __construct(AnswerComponentInterface $component)
    {
        /** @var  Route $route */ //Only for sniffer!!!
        $this->component = $component;
    }

    /**
     * @Route("/answer/removeById", name="removeAnswerById", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            $request = new ByAnswerIdRequest($request);
        } catch (RequestException $e) {
            return $this->returnError($e->getErrors(), $e->getCode());
        }

        try {
            $this->component->deleteById($request->getId());
        } catch (NotFoundException $e) {
            return $this->returnError(['Answer with ID ' . $request->getId()->toString() . ' not found'], 400, $e);
        } catch (Throwable $e) {
            return $this->returnError([$e->getMessage()], 500, $e);
        }

        return $this->returnSuccess([]);
    }
}
