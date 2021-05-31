<?php

namespace App\Controller\Answer;

use App\Component\Answer\AnswerComponentInterface;
use App\Controller\BaseController;
use App\Doctrine\Exception\NotFoundException;
use App\Request\Answer\UpdateAnswerRequest;
use App\Request\RequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class UpdateAnswerByIdController extends BaseController
{
    /**
     * @var AnswerComponentInterface
     */
    protected $component;

    /**
     * UpdateAnswerByIdController constructor.
     * @param AnswerComponentInterface $component
     */
    public function __construct(AnswerComponentInterface $component)
    {
        /** @var  Route $route */ //Only for sniffer!!!
        $this->component = $component;
    }

    /**
     * @Route("/answer/updateById", name="updateAnswerById", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            $updateAnswerRequest = new UpdateAnswerRequest($request);
        } catch (RequestException $e) {
            return $this->returnError($e->getErrors(), $e->getCode());
        }

        try {
            $result = $this->component->update($updateAnswerRequest)->toArray();
        } catch (NotFoundException $e) {
            return $this->returnError(['Answer with ID ' . $updateAnswerRequest->getId()->toString() . ' not found'], 400, $e);
        } catch (Throwable $e) {
            return $this->returnError([$e->getMessage()], $e->getCode() ?: 500, $e);
        }

        return $this->returnSuccess($result);
    }
}
