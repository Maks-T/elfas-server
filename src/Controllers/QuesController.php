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
use Elfas\Services\QuesService;

class QuesController extends Controller
{

  private QuesService $quesService;

  private QuesRepository $quesRepository;

  private LearnRepository $learnRepository;

  private AuthRepository $authRepository;

  private UserRepository $userRepository;

  public function __construct()
  {
    parent::__construct();
    $this->quesService = new QuesService();
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
    } else {
      $questions = $this->quesRepository->getAllQuestions($userId);
    }

    $this->quesService->sendMsgQuestionsGot($questions);
  }

  public function update(): void
  {
    //ToDo prohibit updating without a password

    $quesData = $this->request->getData();

    $userId = $this->findUserByData($quesData)->id;

    $this->checkQuesUpdate($quesData);

    $quesModel = new Ques($quesData);

    $question = $this->quesRepository->updateQuestionById( $quesData['id'], $userId,$quesModel);

    if ($question) {

      $this->quesService->sendMsgQuestionUpdated($question);
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

  public function checkQuesUpdate(array $ques): void
  {
    $errors = [];

    if (!array_key_exists('id', $ques)) {
      $errors[] = 'phrase Id is not transmitted';
    }

    if (!array_key_exists('en', $ques)) {
      $errors[] = 'phrase En is not transmitted';
    }

    if (!array_key_exists('ru', $ques)) {
      $errors[] = 'phrase Ru is not transmitted';
    }

    if (count($errors)) {
      AppException::ThrowBadRequest($errors, __METHOD__);
    }
  }


  public function checkQuesCreate(array $ques): void
  {
    $errors = [];

    if (!array_key_exists('en', $ques)) {
      $errors[] = 'phrase En is not transmitted';
    }
    if (!array_key_exists('ru', $ques)) {
      $errors[] = 'phrase Ru is not transmitted';
    }

    if (count($errors)) {
      AppException::ThrowBadRequest($errors, __METHOD__);
    }
  }
}
