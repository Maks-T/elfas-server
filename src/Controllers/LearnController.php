<?php

declare(strict_types=1);

namespace Elfas\Controllers;

use Elfas\DB\Models\Learn;
use Elfas\DB\Models\User;
use Elfas\DB\Models\Ques;
use Elfas\DB\Repositories\QuesRepository;
use Elfas\DB\Repositories\LearnRepository;
use Elfas\DB\Repositories\AuthRepository;
use Elfas\DB\Repositories\UserRepository;
use Elfas\Exceptions\AppException;
use Elfas\Services\LearnService;

class LearnController extends Controller
{

  private LearnService $learnService;

  private QuesRepository $quesRepository;

  private LearnRepository $learnRepository;

  private AuthRepository $authRepository;

  private UserRepository $userRepository;

  public function __construct()
  {
    parent::__construct();
    $this->learnService = new LearnService();
    $this->quesRepository = new QuesRepository();
    $this->learnRepository = new LearnRepository();
    $this->authRepository = new AuthRepository();
    $this->userRepository = new UserRepository();
  }

  public function create(): void
  {
    $quesData = $this->request->getData();

    $userId = $this->findUserByData($quesData)->id;

    $quesModels = [];
    $leanQuesModels = [];

    $this->checkQuesData($quesData);

    foreach ($quesData['questions'] as $ques) {
      $ques = (array)$ques;

      $this->checkQuesCreate($ques);

      if (isset($ques['id'])) {
        unset($ques['id']);
      }

      $quesModel = new Ques($ques);

      $quesModels[] =  $quesModel;
      $leanQuesModels[] = new Learn(['id' =>  $quesModel->id]);
    }

    $questions = $this->quesRepository->createQuestions($userId, $quesModels);
    $learnQuestions = $this->learnRepository->createLearnQuestions($userId, $leanQuesModels);
    $rR = new \Elfas\DB\Repositories\RepeatRepository();
    $rR->createLearnQuestions($userId, $leanQuesModels);

    if ($questions && $learnQuestions) {

      $this->quesService->sendMsgQuestionsCreated($questions);
      return;
    }

    AppException::ThrowServiceUnavailable('For some reason, the questions are not created', __METHOD__);
  }

  public function get(): void
  {

    $quesData = $this->request->getData();

    $userId = $this->findUserByData($quesData)->id;

    if (array_key_exists('count', $quesData)) {
      $count = $quesData['count'];
      $start = key_exists('start', $quesData) ? $quesData['start'] : 0;

      $questions = $this->quesRepository->getQuestions($userId, $count, $start);
      $learnQuestions = $this->learnRepository->getLearnQuestions($userId, $count, $start);
    } else {
      $questions = $this->quesRepository->getAllQuestions($userId);
      $learnQuestions = $this->learnRepository->getAllLearnQuestions($userId);
    }

    $this->learnService->sendMsgQuestionsGot($questions, $learnQuestions);
  }

  public function update(): void
  {
    //ToDo prohibit updating without a password

    $quesData = $this->request->getData();

    $userId = $this->findUserByData($quesData)->id;

    $this->checkLearnUpdate($quesData);

    foreach ($quesData['succesIds'] as $learn) {

      $learnModel = new Learn($learn);

      $quesModels[] =  $quesModel;
      $leanQuesModels[] = new Learn(['id' =>  $quesModel->id]);
    }


    $quesModel = new Ques($quesData);

    $question = $this->quesRepository->updateQuestionById($quesData['id'], $userId, $quesModel);

    if ($question) {

      $this->learnService->sendMsgQuestionUpdated($question);
      return;
    }

    AppException::ThrowServiceUnavailable('For some reason, the questions are not updated', __METHOD__);
  }

  public function delete(): void
  {
    $quesData = $this->request->getData();

    $userId = $this->findUserByData($quesData)->id;

    $question = $this->quesRepository->deleteQuestionById($quesData['id'], $userId);

    if ($question) {
      $this->quesService->sendMsgQuestionDeleted($question);

      return;
    }

    AppException::ThrowServiceUnavailable('For some reason, the questions are not deleted', __METHOD__);
  }

  private function findUserByData($quesData): User
  {
    if (!array_key_exists('userId', $quesData)) {
      AppException::ThrowBadRequest('userId is not transmitted', __METHOD__);
    }

    $userId = $quesData['userId'] ?? null;

    $user = $userId ? $this->userRepository->getUserById($userId) : null;

    if ($user) {
      return $user;
    }

    AppException::ThrowResourceNotFound("The user with id=$userId does not exist", __METHOD__);
  }

  public function checkQuesData($quesData): void
  {
    $errors = [];

    if (!array_key_exists('questions', $quesData)) {
      $errors[] = 'questions is not transmitted';
    } else {
      if (!is_array($quesData['questions'])) {
        $errors[] = 'questions is not array of question';
      }
    }

    if (count($errors)) {
      AppException::ThrowBadRequest($errors, __METHOD__);
    }
  }

  public function checkLearnUpdate(array $quesLearn): void
  {
    $errors = [];

    if (!array_key_exists('id', $quesLearn)) {
      $errors[] = 'phrase Id is not transmitted';
    }

    if (!array_key_exists('successIds', $quesLearn)) {
      $errors[] = 'phrase successIds is not transmitted';
    }

    if (!array_key_exists('mistakeIds', $quesLearn)) {
      $errors[] = 'phrase mistakeIds is not transmitted';
    }

    if (count($errors)) {
      AppException::ThrowBadRequest($errors, __METHOD__);
    }
  }
}
