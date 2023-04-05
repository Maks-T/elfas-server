<?php

declare(strict_types=1);

namespace Elfas\Controllers;

use Elfas\DB\Model\User;
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
  }

  public function create(): void
  {
    $userData = $this->request->getData();
    $this->checkUserData($userData);
    if (isset($userData['id'])) {
      unset($userData['id']);
    }

    //$userData['password'] = $this->serviceJWT->createTokenByPassword($userData['password']);
    $userModel = new User($userData);
    $user = $this->userRepository->createUser($userModel);

    if ($user) {
      /* $this->userService->setUserCookies($user);
      $this->userService->sendSuccessMessage($user);*/

      return;
    }
  }

  public function get(): void
  {
    $user = $this->userRepository->getUserById($_GET['id']);
    /*  $this->userService->isUserNotFound($user);
    $this->userService->sendSuccessMessage($user);*/
  }

  public function update(): void
  {
    $userData = $this->request->getData();
    $userModel = new User($userData);
    $user = $this->userRepository->updateUserById($userModel->id, $userModel);
    /*  $this->userService->isUserNotFound($user);
    $this->userService->sendSuccessMessage($user);*/
  }

  public function delete(): void
  {
    $user = $this->userRepository->deleteUserById($_GET['id']);
    /*  $this->userService->isUserNotFound($user);
    $this->userService->sendSuccessMessage($user, 204);*/
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
    if (!isset($data['password']) || (strlen($data['password']) < 6)) {
      $errors['password'] = 'Password is empty or its length is less than 6 characters';
    } else {
      $regexPassword = '/(?:[а-яёa-z]\d|\d[в-яёa-z])/i';

      if (!preg_match($regexPassword, $data['password'])) {
        $errors['password'] = 'Your password must contain both letters and numbers';
      }
    }

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
      $regexName = '/^[a-zA-Z]+$/';

      if (!preg_match($regexName, $data['name'])) {

        $errors['name'] = 'The name must contain only letters';
      }
    }

    if (count($errors)) {
      AppException::ThrowBadRequest($errors, __METHOD__);
    }
  }
}
