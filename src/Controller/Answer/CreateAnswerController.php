<?php

namespace App\Controller\Answer;

use App\Controller\BaseController;
use App\Component\Answer\AnswerComponentInterface;
use App\Request\Answer\CreateAnswerRequest;
use App\Request\RequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class CreateAnswerController extends BaseController
{
    /**
     * @var AnswerComponentInterface
     */
    protected $component;

    /**
     * CreateAnswerController constructor.
     * @param AnswerComponentInterface $component
     */
    public function __construct(AnswerComponentInterface $component)
    {
        /** @var  Route $route */ //Only for sniffer!!!
        $this->component = $component;
    }

    /**
     * @Route("/answer/create", name="createAnswer", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            $createAnswerRequest = new CreateAnswerRequest($request);
        } catch (RequestException $e) {
            return $this->returnError($e->getErrors(), $e->getCode());
        }

        try {
            $result = $this->component->create($createAnswerRequest)->toArray();
        } catch (Throwable $e) {
            return $this->returnError([$e->getMessage()], 500, $e);
        }

        return $this->returnSuccess($result);
    }
}
