<?php

declare(strict_types=1);

namespace Elfas\Controllers;

use Elfas\DB\Models\User;
use Elfas\DB\Repositories\AuthRepository;
use Elfas\DB\Repositories\UserRepository;
use Elfas\Exceptions\AppException;
use Elfas\Services\UserService;

class UserController extends Controller
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

  public function create(): void
  {
    $userData = $this->request->getData();

    $this->checkUserDataCreate($userData);

    if (isset($userData['id'])) {
      unset($userData['id']);
    }

    $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
    $userModel = new User($userData);
    $user = $this->userRepository->createUser($userModel);

    if ($user) {

      $publicKey = $this->authRepository->getPublicKey($user->id, $userData['clientKey']);
      $this->userService->sendMsgUserCreated($user, $publicKey);
      return;
    }

    AppException::ThrowServiceUnavailable('For some reason, the user is not created', __METHOD__);
  }

  public function get(): void
  {
    $userData = $this->request->getData();

    $userId = $userData['id'] ?? null;

    $user = $userId ? $this->userRepository->getUserById($userId) : null;

    if ($user) {
      $this->userService->sendMsgUserGot($user);
      return;
    }

    AppException::ThrowResourceNotFound("The user with id=$userId does not exist", __METHOD__);
  }

  public function update(): void
  {
    //ToDo prohibit updating without a password or publickKey

    $userData = $this->request->getData();

    $userFinded = $this->findUserByData($userData);

    if ($userFinded) {
      $this->checkUserDataUpdate($userData);

      $userData['login'] = $userData['login'] ?? $userFinded->login;
      $userData['name'] = $userData['name'] ?? $userFinded->name;
      $userData['email'] = $userData['email'] ?? $userFinded->email;
      $userData['password'] = $userData['password'] ?? $userFinded->password;

      $userModel = new User($userData);
      $user = $this->userRepository->updateUserById($userFinded->id, $userModel);

      if ($user) {
        $this->userService->sendMsgUserUpdated($user);
        return;
      }
    }

    AppException::ThrowServiceUnavailable('For some reason, the questions are not updated', __METHOD__);
  }

  public function delete(): void
  {
    //ToDo prohibit updating without a password or publickKey

    $userData = $this->request->getData();

    $userId = $this->findUserByData($userData)->id;

    $user = $this->userRepository->deleteUserById($userId);
    $this->authRepository->deleteByUserId($userId);

    if ($user) {
      $this->userService->sendMsgUserDeleted($user);
      return;
    }

    AppException::ThrowResourceNotFound("The user with id=$userId  does not exist", __METHOD__);
  }

  private function findUserByData($userData): User
  {
    if (!array_key_exists('id', $userData)) {
      AppException::ThrowBadRequest('userId is not transmitted', __METHOD__);
    }

    $userId = $userData['id'];

    $user = $this->userRepository->getUserById($userId);

    if ($user) {
      return $user;
    }

    AppException::ThrowResourceNotFound("The user with id=$userId does not exist", __METHOD__);
  }

  private function checkUserDataUpdate($data): void
  {
    $errors = [];

    //Check clientKey
    if (!isset($data['clientKey'])) {
      $errors['clientKey'] = 'clientKey is not transmitted';
    }

    //Check login
    if (isset($data['login']) && (strlen($data['login']) < 6)) {
      $errors['login'] = 'Login is empty or its length is less than 6 characters';
    }

    //check password
    if (isset($data['password']) &&  (strlen($data['password']) < 3)) {
      $errors['password'] = 'Password is empty or its length is less than 3 characters';
    }/* else {
      $regexPassword = '/(?:[а-яёa-z]\d|\d[в-яёa-z])/i';

      if (!preg_match($regexPassword, $data['password'])) {
        $errors['password'] = 'Your password must contain both letters and numbers';
      }
    }*/

    //check email
    $regexEmail = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';

    if (isset($data['email']) &&  !preg_match($regexEmail, strtolower($data['email']))) {
      $errors['email'] = 'Email is invalid';
    }

    //check name
    if (isset($data['name']) &&  (strlen($data['name']) < 2)) {
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

  private function checkUserDataCreate($data): void
  {
    $errors = [];

    //Check clientKey
    if (!isset($data['clientKey'])) {
      $errors['clientKey'] = 'clientKey is not transmitted';
    }

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
