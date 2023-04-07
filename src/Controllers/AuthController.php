<?php

declare(strict_types=1);

namespace Elfas\Controllers;

use Elfas\DB\Models\User;
use Elfas\DB\Repositories\UserRepository;
use Elfas\Exceptions\AppException;
use Elfas\Services\UserService;
use Elfas\Services\AuthService;

class AuthController extends Controller
{

  private UserService $userService;

  private AuthService $authService;

  private UserRepository $userRepository;

  public function __construct()
  {
    parent::__construct();
    $this->userService = new UserService();
    $this->authService = new AuthService();
    $this->userRepository = new UserRepository();
  }

  public function login(): void
  {

    ['login' => $login, 'password' => $password] = $this->request->getData();

    $user = $this->userRepository->getUserByLogin($login);

    if (!$user) {
      AppException::ThrowResourceNotFound("The user with login=$login does not exist", __METHOD__);
    }

    //ToDo create verify password
    if (password_verify($password, $user->password)) {

      $this->authService->getPublicKey($user->id);
      $this->userService->sendMsgUserGot($user);
      return;
    }
  }
}
