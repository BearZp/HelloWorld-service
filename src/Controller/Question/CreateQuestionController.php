<?php

namespace App\Controller\Question;

use App\Controller\BaseController;
use App\Component\Question\QuestionComponentInterface;
use App\Doctrine\Exception\NotFoundException;
use App\Request\Question\CreateQuestionRequest;
use App\Request\RequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class CreateQuestionController extends BaseController
{
    /**
     * @var QuestionComponentInterface
     */
    protected $component;

    /**
     * CreateQuestionController constructor.
     * @param QuestionComponentInterface $component
     */
    public function __construct(QuestionComponentInterface $component)
    {
        /** @var  Route $route */ //Only for sniffer!!!
        $this->component = $component;
    }

    /**
     * @Route("/question/create", name="createQuestion", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            $createQuestionRequest = new CreateQuestionRequest($request);
        } catch (RequestException $e) {
            return $this->returnError($e->getErrors(), $e->getCode());
        }

        try {
            $result = $this->component->create($createQuestionRequest)->toArray();
        } catch (NotFoundException $e) {
            return $this->returnError([$e->getMessage()], 404, $e);
        } catch (Throwable $e) {
            return $this->returnError([$e->getMessage()], 500, $e);
        }

        return $this->returnSuccess($result);
    }
}
