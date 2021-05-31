<?php

namespace App\Controller\Answer;

use App\Component\Answer\AnswerComponentInterface;
use App\Component\Question\QuestionComponentInterface;
use App\Controller\BaseController;
use App\Doctrine\Exception\NotFoundException;
use App\Request\Answer\ByAnswerIdRequest;
use App\Request\Question\ByQuestionIdRequest;
use App\Request\RequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class GetAnswerByIdController extends BaseController
{
    /**
     * @var AnswerComponentInterface
     */
    protected $component;

    /**
     * GetAnswerByIdController constructor.
     * @param AnswerComponentInterface $component
     */
    public function __construct(AnswerComponentInterface $component)
    {
        /** @var  Route $route */ //Only for sniffer!!!
        $this->component = $component;
    }

    /**
     * @Route("/answer/getById", name="getAnswerById", methods={"POST"})
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
            $result = $this->component->getById($request->getId())->toArray();
        } catch (NotFoundException $e) {
            return $this->returnError(['Answer with ID ' . $request->getId()->toString() . ' not found'], 400, $e);
        } catch (Throwable $e) {
            return $this->returnError([$e->getMessage()], $e->getCode() ?: 500, $e);
        }

        return $this->returnSuccess($result);
    }
}
