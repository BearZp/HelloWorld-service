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

class RemoveQuestionByIdController extends BaseController
{
    /**
     * @var QuestionComponentInterface
     */
    protected $component;

    /**
     * RemoveQuestionByIdController constructor.
     * @param QuestionComponentInterface $component
     */
    public function __construct(QuestionComponentInterface $component)
    {
        /** @var  Route $route */ //Only for sniffer!!!
        $this->component = $component;
    }

    /**
     * @Route("/question/removeById", name="removeQuestionById", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            $request = new ByQuestionIdRequest($request);
        } catch (RequestException $e) {
            return $this->returnError($e->getErrors(), $e->getCode());
        }

        try {
            $this->component->deleteById($request->getId());
        } catch (NotFoundException $e) {
            return $this->returnError(['Question with ID ' . $request->getId()->toString() . ' not found'], 400, $e);
        } catch (Throwable $e) {
            return $this->returnError([$e->getMessage()], 500, $e);
        }

        return $this->returnSuccess([]);
    }
}
