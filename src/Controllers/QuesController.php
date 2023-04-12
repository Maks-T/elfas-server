<?php

declare(strict_types=1);

namespace Elfas\Controllers;

use Elfas\DB\Models\User;
use Elfas\DB\Models\Ques;
use Elfas\DB\Repositories\QuesRepository;
use Elfas\DB\Repositories\AuthRepository;
use Elfas\DB\Repositories\UserRepository;
use Elfas\Exceptions\AppException;
use Elfas\Services\UserService;
use Elfas\Services\QuesService;

class QuesController extends Controller
{

  private UserService $userService;

  private QuesService $quesService;

  private QuesRepository $quesRepository;

  private AuthRepository $authRepository;

  private UserRepository $userRepository;

  public function __construct()
  {
    parent::__construct();
    $this->userService = new UserService();
    $this->quesService = new QuesService();
    $this->userRepository = new QuesRepository();
    $this->authRepository = new AuthRepository();
    $this->userRepository = new UserRepository();
  }

  public function create(): void
  {
    $quesData = $this->request->getData();

    $this->checkQuesData($quesData);

    $userId = $quesData['userId'];

    $quesModels = [];
    foreach ($quesData as $ques) {
      $this->checkQues($ques);
      if (isset($ques['id'])) {
        unset($ques['id']);
      }

      $quesModels = new Ques($ques);
    }

    $questions = $this->quesRepository->createQuestions($userId, $quesModels);

    if ($questions) {

      $this->quesService->sendMsgQuestionsCreated($questions);
      return;
    }

    AppException::ThrowServiceUnavailable('For some reason, the questions are not created', __METHOD__);
  }

  public function get(): void
  {

    $this->checkQuesData($_GET);

    $userId = $_GET['userId'];

    if (array_key_exists('count', $_GET)) {
      $count = $_GET['count'];
      $start = key_exists('start', $_GET) ? $_GET['start'] : 0;

      $questions = $this->quesRepository->getQuestions($userId, $count, $start);
    } else {
      $questions = $this->quesRepository->getAllQuestions($userId);
    }

    $this->quesService->sendMsgQuestionsGot($questions);
  }

  public function update(): void
  {
    //ToDo prohibit updating without a password

    $this->checkQuesData($_GET);

    $userId = $_GET['id'];

    $quesData = $this->request->getData();

    if (isset($quesData['id'])) {
      unset($quesData['id']);
    }

    $this->checkQues($quesData);

    $quesModel = new Ques($quesData);

    $questions = $this->quesRepository->updateQuestionById($userId, $quesModel);

    if ($questions) {

      $this->quesService->sendMsgQuestionsCreated($questions);
      return;
    }

    AppException::ThrowServiceUnavailable('For some reason, the questions are not created', __METHOD__);
  }

  public function delete(): void
  {
    $userId = $_GET['id'];
    $user = $this->userRepository->deleteUserById($userId);
    $this->authRepository->deleteByUserId($userId);

    if ($user) {
      $this->userService->sendMsgUserDeleted($user);
      return;
    }

    AppException::ThrowResourceNotFound("The user with id=$userId  does not exist", __METHOD__);
  }

  public function checkQuesData($quesData): void
  {
    if (!array_key_exists('userId', $quesData)) {
      AppException::ThrowBadRequest('userId is not transmitted', __METHOD__);
    }
    $userId = $quesData['userId'];

    $user = $this->userRepository->getUserById($userId);

    if (!$user) {
      AppException::ThrowResourceNotFound("The user with login=$userId does not exist", __METHOD__);
    }
  }

  public function checkQues($ques): void
  {
    $errors = [];

    if (!array_key_exists('en', $ques)) {
      $errors[] = 'phrase En is not transmitted';
    }
    if (!array_key_exists('clientKey', $ques)) {
      $errors[] = 'phrase Ru is not transmitted';
    }

    if (count($errors)) {
      AppException::ThrowBadRequest($errors, __METHOD__);
    }
  }
}
