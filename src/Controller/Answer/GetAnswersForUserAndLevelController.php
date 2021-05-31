<?php

namespace App\Controller\Answer;

use App\Component\Answer\AnswerComponentInterface;
use App\Component\Question\QuestionComponentInterface;
use App\Controller\BaseController;
use App\Doctrine\Exception\NotFoundException;
use App\Request\Answer\ByUserAndLevelRequest;
use App\Request\RequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class GetAnswersForUserAndLevelController extends BaseController
{
    /**
     * @var AnswerComponentInterface
     */
    protected $component;

    /**
     * GetAnswersForUserAndLevelController constructor.
     * @param AnswerComponentInterface $component
     */
    public function __construct(AnswerComponentInterface $component)
    {
        /** @var  Route $route */ //Only for sniffer!!!
        $this->component = $component;
    }

    /**
     * @Route("/answer/getForUserAndLevel", name="getAnswersForUserAndLevel", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            $request = new ByUserAndLevelRequest($request);
        } catch (RequestException $e) {
            return $this->returnError($e->getErrors(), $e->getCode());
        }

        try {
            $result = $this->component->getAnswerListForValidate($request);
        } catch (Throwable $e) {
            return $this->returnError([$e->getMessage()], 500, $e);
        }

        return $this->returnSuccess($result);
    }
}
