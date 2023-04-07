<?php

declare(strict_types=1);

namespace Elfas\Controllers;

use Elfas\DB\Models\User;
use Elfas\DB\Repositories\UserRepository;
use Elfas\Exceptions\AppException;
use Elfas\Services\UserService;

class UserController extends Controller
{

  private UserService $userService;

  private UserRepository $userRepository;

  public function __construct()
  {
    parent::__construct();
    $this->userService = new UserService();
    $this->userRepository = new UserRepository();
  }

  public function create(): void
  {
    $userData = $this->request->getData();

    $this->checkUserData($userData);
    if (isset($userData['id'])) {
      unset($userData['id']);
    }

    $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
    $userModel = new User($userData);
    $user = $this->userRepository->createUser($userModel);

    if ($user) {

      $this->userService->sendMsgUserCreated($user);
      return;
    }

    AppException::ThrowServiceUnavailable('For some reason, the user is not created', __METHOD__);
  }

  public function get(): void
  {
    $userId = $_GET['id'];
    $user = $this->userRepository->getUserById($userId);

    if ($user) {
      $this->userService->sendMsgUserGot($user);
      return;
    }

    AppException::ThrowResourceNotFound("The user with id=$userId does not exist", __METHOD__);
  }

  public function update(): void
  {
    //ToDo prohibit updating without a password
    $userId = $_GET['id'];

    $user = $this->userRepository->getUserById($userId);

    $userData = $this->request->getData();
    $this->checkUserData($userData);

    $userModel = new User($userData);
    $user = $this->userRepository->updateUserById($userId, $userModel);

    if ($user) {
      $this->userService->sendMsgUserUpdated($user);
      return;
    }

    AppException::ThrowResourceNotFound("The user with id=$userId does not exist", __METHOD__);
  }

  public function delete(): void
  {
    $userId = $_GET['id'];
    $user = $this->userRepository->deleteUserById($userId);

    if ($user) {
      $this->userService->sendMsgUserDeleted($user);
      return;
    }

    AppException::ThrowResourceNotFound("The user with id=$userId  does not exist", __METHOD__);
  }

  public function checkUserData($data): void
  {
    $errors = [];

    //Check login
    if (!isset($data['login']) || (strlen($data['login']) < 6)) {
      $errors['login'] = 'Login is empty or its length is less than 6 characters';
    } else {
      $user = $this->userRepository->getUserByLogin($data['login']);

      if ($user) {
        $errors['login'] = 'A user with this login already exists';
      }
    }

    //check password
    if (!isset($data['password']) || (strlen($data['password']) < 3)) {
      $errors['password'] = 'Password is empty or its length is less than 3 characters';
    }/* else {
      $regexPassword = '/(?:[а-яёa-z]\d|\d[в-яёa-z])/i';

      if (!preg_match($regexPassword, $data['password'])) {
        $errors['password'] = 'Your password must contain both letters and numbers';
      }
    }*/

    //check email
    $regexEmail = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';

    if (!isset($data['email']) || !preg_match($regexEmail, strtolower($data['email']))) {
      $errors['email'] = 'Email is invalid';
    }

    $user = $this->userRepository->getUserByEmail($data['email']);

    if ($user) {
      $errors['email'] = 'A user with this email already exists';
    }

    //check name
    if (!isset($data['name']) || (strlen($data['name']) < 2)) {
      $errors['name'] = 'The name must contain at least two letters';
    } else {
      $regexName = '/^[a-zA-Z-\s]+$/';

      if (!preg_match($regexName, $data['name'])) {

        $errors['name'] = 'The name must contain only letters';
      }
    }

    if (count($errors)) {
      AppException::ThrowBadRequest($errors, __METHOD__);
    }
  }
}
