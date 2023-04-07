<?php

declare(strict_types=1);

namespace Elfas\Controllers;

use Elfas\DB\Models\User;
use Elfas\DB\Repositories\UserRepository;
use Elfas\DB\Repositories\AuthRepository;
use Elfas\Exceptions\AppException;
use Elfas\Services\UserService;


class AuthController extends Controller
{

  private UserService $userService;

  private AuthRepository $authRepository;

  private UserRepository $userRepository;

  public function __construct()
  {
    parent::__construct();
    $this->userService = new UserService();
    $this->authRepository = new AuthRepository();
    $this->userRepository = new UserRepository();
  }

  public function login(): void
  {

    $loginData = $this->request->getData();

    $this->checkLoginData($loginData);

    ['login' => $login, 'password' => $password, 'clientKey' => $clientKey] = $loginData;

    /** @var User $user */
    $user = $this->userRepository->getUserByLogin($login);

    if (!$user) {
      AppException::ThrowResourceNotFound("The user with login=$login does not exist", __METHOD__);
    }

    //ToDo create verify password
    if (password_verify($password, $user->password)) {

      $publicKey = $this->authRepository->getPublicKey($user->id, $clientKey);
      $this->userService->sendMsgUserGot($user, $publicKey);
      return;
    }
  }

  private function checkLoginData(array $loginData)
  {
    $errors = [];

    if (!array_key_exists('login', $loginData)) {
      $errors[] = 'login not transmitted';
    }
    if (!array_key_exists('password', $loginData)) {
      $errors[] = 'password not transmitted';
    }
    if (!array_key_exists('clientKey', $loginData)) {
      $errors[] = 'clientKey not transmitted';
    }

    if (count($errors)) {
      AppException::ThrowBadRequest($errors, __METHOD__);
    }
  }

}
