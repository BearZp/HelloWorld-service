<?php

namespace App\Controller\Question;

use App\Component\Question\QuestionComponentInterface;
use App\Controller\BaseController;
use App\Doctrine\Exception\NotFoundException;
use App\Request\Question\UpdateQuestionRequest;
use App\Request\RequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class UpdateQuestionByIdController extends BaseController
{
    /**
     * @var QuestionComponentInterface
     */
    protected $component;

    /**
     * UpdateQuestionByIdController constructor.
     * @param QuestionComponentInterface $component
     */
    public function __construct(QuestionComponentInterface $component)
    {
        /** @var  Route $route */ //Only for sniffer!!!
        $this->component = $component;
    }

    /**
     * @Route("/question/updateById", name="updateQuestionById", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            $updateQuestionRequest = new UpdateQuestionRequest($request);
        } catch (RequestException $e) {
            return $this->returnError($e->getErrors(), $e->getCode());
        }

        try {
            $result = $this->component->update($updateQuestionRequest)->toArray();
        } catch (NotFoundException $e) {
            return $this->returnError(['Question with ID ' . $updateQuestionRequest->getId()->toString() . ' not found'], 400, $e);
        } catch (Throwable $e) {
            return $this->returnError([$e->getMessage()], 500, $e);
        }

        return $this->returnSuccess($result);
    }
}
