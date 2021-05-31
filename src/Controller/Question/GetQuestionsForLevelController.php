<?php

namespace App\Controller\Question;

use App\Component\Question\QuestionComponentInterface;
use App\Controller\BaseController;
use App\Doctrine\Exception\NotFoundException;
use App\Request\Question\ByLevelIdRequest;
use App\Request\RequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class GetQuestionsForLevelController extends BaseController
{
    /**
     * @var QuestionComponentInterface
     */
    protected $component;

    /**
     * GetQuestionsForLevelController constructor.
     * @param QuestionComponentInterface $component
     */
    public function __construct(QuestionComponentInterface $component)
    {
        /** @var  Route $route */ //Only for sniffer!!!
        $this->component = $component;
    }

    /**
     * @Route("/question/getForLevel", name="getQuestionsForLevel", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            $request = new ByLevelIdRequest($request);
        } catch (RequestException $e) {
            return $this->returnError($e->getErrors(), $e->getCode());
        }

        try {
            $result = $this->component->getQuestionsForLevel($request->getId());
        } catch (NotFoundException $e) {
            return $this->returnError(['There are no questions for level ' . $request->getId()->toString()], 400, $e);
        } catch (Throwable $e) {
            return $this->returnError([$e->getMessage()], 500, $e);
        }

        return $this->returnSuccess($result);
    }
}
