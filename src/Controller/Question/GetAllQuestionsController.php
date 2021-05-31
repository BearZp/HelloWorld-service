<?php

namespace App\Controller\Question;

use App\Component\Question\QuestionComponentInterface;
use App\Controller\BaseController;
use App\Doctrine\Exception\NotFoundException;
use App\Request\Question\ByQuestionIdRequest;
use App\Request\RequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class GetAllQuestionsController extends BaseController
{
    /**
     * @var QuestionComponentInterface
     */
    protected $component;

    /**
     * GetAllQuestionsController constructor.
     * @param QuestionComponentInterface $component
     */
    public function __construct(QuestionComponentInterface $component)
    {
        /** @var  Route $route */ //Only for sniffer!!!
        $this->component = $component;
    }

    /**
     * @Route("/question/getAll", name="getAllQuestions", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            $result = $this->component->getAll();
        } catch (Throwable $e) {
            return $this->returnError([$e->getMessage()], 500, $e);
        }

        return $this->returnSuccess($result);
    }
}
