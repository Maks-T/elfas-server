<?php

declare(strict_types=1);

namespace Elfas\Controllers;

use Elfas\DB\Models\User;
use Elfas\DB\Repositories\UserRepository;
use Elfas\DB\Repositories\AuthRepository;
use Elfas\Exceptions\AppException;
use Elfas\Services\UserService;

//ToDo auth Service

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

  public function loginByKeys(): void
  {

    $keysData = $this->request->getData();

    $this->checkKeysData($keysData);

    ['publicKey' => $publicKey, 'clientKey' => $clientKey, 'userId' => $userKey] = $keysData;

    /** @var User $user */
    $user = $this->userRepository->getUserById($userKey);

    if (!$user) {
      AppException::ThrowResourceNotFound("The user with id=$userKey does not exist", __METHOD__);
    }

    $publicKeyNew = $this->authRepository->checkKeys($userKey, $clientKey, $publicKey);

    if ($publicKeyNew) {
      $this->userService->sendMsgUserGot($user, $publicKeyNew);

      return;
    }

    AppException::ThrowForbidden('Incorrect authorization keys were passed', __METHOD__);
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

  private function checkKeysData(array $keysData)
  {
    $errors = [];

    if (!array_key_exists('publicKey', $keysData)) {
      $errors[] = 'publicKey is not transmitted';
    }
    if (!array_key_exists('clientKey', $keysData)) {
      $errors[] = 'clientKey is not transmitted';
    }
    if (!array_key_exists('userId', $keysData)) {
      $errors[] = 'userId is not transmitted';
    }


    if (count($errors)) {
      AppException::ThrowBadRequest($errors, __METHOD__);
    }
  }
}
