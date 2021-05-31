<?php

namespace App\Component\Answer;

use App\Component\BaseComponent;
use App\Component\Directory\DirectoryComponentInterface;
use App\Component\Level\LevelComponentInterface;
use App\Component\Question\QuestionComponentInterface;
use App\Component\UserLevel\UserLevelComponentInterface;
use App\DataType\AnswerId;
use App\DataType\AnswerValue;
use App\DataType\DirectoryName;
use App\Doctrine\Exception\NotFoundException;
use App\Model\Answer\Answer;
use App\Model\Answer\AnswerCollection;
use App\Model\Answer\AnswerFuture;
use App\Model\Answer\AnswerInterface;
use App\Model\Answer\AnswerMapper;
use App\Model\Answer\AnswerStatusEnum;
use App\Model\Answer\AnswerTypeEnum;
use App\Model\Directory\DirectoryInterface;
use App\Model\Level\Level;
use App\Model\Question\QuestionInterface;
use App\Model\UserLevel\UserLevelStatusEnum;
use App\Repository\Answer\AnswerRepositoryInterface;
use App\Request\Answer\ByUserAndLevelRequest;
use App\Request\Answer\CreateAnswerRequest;
use App\Request\Answer\UpdateAnswerRequest;
use Doctrine\DBAL\Exception as DoctrineDBALException;
use Doctrine\DBAL\Driver\Exception as DoctrineDBALDriverException;
use Exception;

class AnswerComponent extends BaseComponent implements AnswerComponentInterface
{
    /** @var AnswerRepositoryInterface */
    protected $repository;

    /** @var QuestionComponentInterface */
    protected $questionComponent;

    /** @var UserLevelComponentInterface */
    protected $userLevelComponent;

    /** @var LevelComponentInterface */
    protected $levelComponent;

    /** @var DirectoryComponentInterface */
    protected $directoryComponent;

    /**
     * AnswerComponent constructor.
     * @param AnswerRepositoryInterface $repository
     * @param QuestionComponentInterface $questionComponent
     * @param UserLevelComponentInterface $userLevelComponent
     * @param LevelComponentInterface $levelComponent
     * @param DirectoryComponentInterface $directoryComponent
     */
    public function __construct(
        AnswerRepositoryInterface $repository,
        QuestionComponentInterface $questionComponent,
        UserLevelComponentInterface $userLevelComponent,
        LevelComponentInterface $levelComponent,
        DirectoryComponentInterface $directoryComponent
    ) {
        $this->repository = $repository;
        $this->questionComponent = $questionComponent;
        $this->userLevelComponent = $userLevelComponent;
        $this->levelComponent = $levelComponent;
        $this->directoryComponent = $directoryComponent;
    }

    /**
     * @param AnswerId $id
     * @return AnswerInterface
     * @throws DoctrineDBALException
     */
    public function getById(AnswerId $id): AnswerInterface
    {
        $answer = $this->repository->getById($id);
        try {
            $answer->getId();
        } catch (NotFoundException $e) {
            throw new Exception('Answer with ID ' . $id->toString() . ' not fount', 404);
        }
        return $answer;
    }

    /**
     * @param CreateAnswerRequest $request
     * @return AnswerInterface
     * @throws DoctrineDBALDriverException
     * @throws DoctrineDBALException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Throwable
     */
    public function create(CreateAnswerRequest $request): AnswerInterface
    {
        $answer = $this->repository->getAnswerForQuestion($request->getQuestionId(), $request->getUserId());
        $question = $this->questionComponent->getById($request->getQuestionId());
        $userLevel = $this->userLevelComponent->getUserLevel($request->getUserId());
        $questionLevelCollection = $this->questionComponent->getLevelCollectionForQuestion($request->getQuestionId());

        try {
            $answer->getId();
            $answerExist = true;
        } catch (NotFoundException $e) {
            $answerExist = false;
        }

        if ($answerExist) {
            // resolve future objects for close db connections
            $question->getId();
            //trow logic exception
            throw new Exception('Answer already exist', 400);
        }

        $answer = new AnswerFuture(function () use ($question, $request) {
            return new Answer(
                new AnswerId($this->repository->getNewUuidId()->toString()),
                $question->getId(),
                $request->getUserId(),
                $request->getType(),
                new AnswerStatusEnum(AnswerStatusEnum::NEED_VERIFICATION),
                $request->getValue(),
                new AnswerMapper()
            );
        });

        //Todo: validate user ???
        $this->validateAnswerData(
            $request->getType(),
            $request->getValue(),
            $question
        );

        $answer = $this->repository->create($answer);
        $userLevelStatus = new UserLevelStatusEnum(UserLevelStatusEnum::NEED_VERIFICATION);

        $needToVerify = false;
        /** @var Level $level */
        foreach ($questionLevelCollection as $level) {
            $needToVerify = $needToVerify || $userLevel->getNextLevelId()->isEqual($level->getId());
        }

        if ($needToVerify) {
            $this->userLevelComponent->setUserLevelStatus(
                $userLevel->getUserId(),
                $userLevelStatus
            );
        }

        return $answer;
    }

    /**
     * @param UpdateAnswerRequest $request
     * @return AnswerInterface
     * @throws DoctrineDBALException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Throwable
     */
    public function update(UpdateAnswerRequest $request): AnswerInterface
    {
        $answer = $this->repository->getById($request->getId());
        $question = $this->questionComponent->getById($request->getQuestionId());
        $questionLevelCollection = $this->questionComponent->getLevelCollectionForQuestion($request->getQuestionId());
        try {
            $userLevel = $this->userLevelComponent->getUserLevel($answer->getUserId());
        } catch (NotFoundException $e) {
            throw new Exception('Answer with ID ' . $request->getId()->toString() . ' not fount', 404);
        }

        $this->validateAnswerData(
            $request->getType(),
            $request->getValue(),
            $question
        );

        $newAnswer =  new Answer(
            $answer->getId(),
            $request->getQuestionId(),
            $answer->getUserId(),
            $request->getType(),
            new AnswerStatusEnum(AnswerStatusEnum::NEED_VERIFICATION),
            $request->getValue(),
            $answer->getMapper()
        );

        $newAnswer = $this->repository->update($newAnswer);
        $userLevelStatus = new UserLevelStatusEnum(UserLevelStatusEnum::NEED_VERIFICATION);

        $needToDowngrade = false;
        /** @var Level $level */
        foreach ($questionLevelCollection as $level) {
            $needToDowngrade = $needToDowngrade || $userLevel->getLevelId()->isEqual($level->getId());
        }

        if ($needToDowngrade) {
            $this->userLevelComponent->setUserLevelStatus(
                $userLevel->getUserId(),
                $userLevelStatus
            );
        }

        return $newAnswer;
    }

    /**
     * @param AnswerId $id
     * @return AnswerInterface
     * @throws DoctrineDBALException
     */
    public function validateAnswer(AnswerId $id): AnswerInterface
    {
        // get answer
        $answer = $this->repository->getById($id);
        // get user level
        $userLevel = $this->userLevelComponent->getUserLevel($answer->getUserId());

        $newAnswerModel = new Answer(
            $answer->getId(),
            $answer->getQuestionId(),
            $answer->getUserId(),
            $answer->getType(),
            new AnswerStatusEnum(AnswerStatusEnum::VERIFIED),
            $answer->getValue(),
            $answer->getMapper()
        );
        // save answer
        $answer = $this->repository->update($newAnswerModel);

        // get questions for next user level
        $questionCollection = $this->questionComponent->getQuestionCollectionForLevel($userLevel->getNextLevelId());

        // collect answers of user for questions (async)
        $userAnswersCollection = [];
        /** @var QuestionInterface $question */
        foreach ($questionCollection as $question) {
            $userAnswersCollection[] = $this->repository->getAnswerForQuestion($question->getId(), $answer->getUserId());
        }

        // verify answers status
        $allVerified = false;
        foreach ($userAnswersCollection as $userAnswer) {
            $allVerified = $allVerified && $userAnswer->getStatus()->getValue() === AnswerStatusEnum::VERIFIED;
        }

        // upgrade user level
        if ($allVerified) {
            $this->userLevelComponent->upgrade($newAnswerModel->getUserId());
        }

        return $answer;
    }

    /**
     * @param AnswerTypeEnum $answerType
     * @param AnswerValue $answerValue
     * @param QuestionInterface $question
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Throwable
     */
    protected function validateAnswerData(
        AnswerTypeEnum $answerType,
        AnswerValue $answerValue,
        QuestionInterface $question
    ): void {
        if ($answerType->getValue() != $question->getType()->getValue()) {
            throw new Exception('Answer type do not equal to question type', 400);
        }

        if ($answerType->isEqual(new AnswerTypeEnum(AnswerTypeEnum::SELECT))) {
            $directoryCollection = $this->directoryComponent->getDirectoryForQuestion($question->getId());
            $isset = false;
            $directoryName = new DirectoryName('');
            /** @var DirectoryInterface $item */
            foreach ($directoryCollection as $item) {
                $directoryName = $item->getDirectoryName();
                if ($item->getDirectoryValue()->toString() === $answerValue->toString()) {
                    $isset = true;
                    break;
                }
            }
            if (!$isset) {
                throw new Exception('Answer value (' . $answerValue->toString() . ') do not found 
                in question directory ' . $directoryName->toString(), 400);
            }
        }
    }


    /**
     * @param AnswerId $id
     * @return bool
     * @throws DoctrineDBALException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function deleteById(AnswerId $id): bool
    {
        $answer = $this->repository->getById($id);
        $userLevel = $this->userLevelComponent->getUserLevel($answer->getUserId());
        $questionLevels = $this->questionComponent->getLevelCollectionForQuestion($answer->getQuestionId());

        /** @var Level $level */
        foreach ($questionLevels as $level) {
            if ($level->getId()->isEqual($userLevel->getLevelId())) {
                $this->userLevelComponent->downgrade(
                    $answer->getUserId(),
                    $level->getParentLevel()
                );
                break;
            }
        }

        return $this->repository->deleteById($id);
    }

    /**
     * @param ByUserAndLevelRequest $request
     * @return array
     * @throws DoctrineDBALException
     */
    public function getAnswerListForValidate(ByUserAndLevelRequest $request): array
    {
        $questions = $this->questionComponent->getQuestionCollectionForLevel($request->getLevelId());

        $answers = [];
        /** @var QuestionInterface $question */
        foreach ($questions as $question) {
            $answers[] = $this->repository->getAnswerForQuestion($question->getId(), $request->getUserId());
        }

        foreach ($answers as &$answer) {
            try {
                $answer->getId();
            } catch (NotFoundException $e) {
                $answer = null;
            }
        }

        $answers = new AnswerCollection(array_filter($answers));
        $result = $this->questionComponent->getQuestionsForLevel($request->getLevelId());
        $result['answers'] = $answers->toArray();
        return $result;
    }
}
