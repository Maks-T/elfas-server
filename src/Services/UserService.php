<?php

declare(strict_types=1);

namespace Elfas\Services;

use Elfas\DB\Models\User;
use Elfas\Exceptions\AppException;

class UserService
{
  public function sendSuccessMessage(User $user, $code = 200)
  {
    http_response_code($code);

    echo json_encode([
      'status' => 'success',
      'user' => $user->response()
    ]);
  }

  public function isUserNotFound(?User $user)
  {
    if (!$user) {
      throw new \Exception(
        json_encode(['status' => 'error', 'message' => "User with id='{$_GET['id']}' doesn't exist"]),
        AppException::NOT_FOUND
      );
    }
  }

  public function isUserNotFoundByLogin(?User $user)
  {
    if (!$user) {
      throw new \Exception(
        json_encode([
          'status' => 'error',
          'errors' => [
            'login' => "A user with this login does not exist!",
          ]
        ]),
        AppException::NOT_FOUND
      );
    }
  }

  public function isPasswordInvalid()
  {

    throw new \Exception(
      json_encode([
        'status' => 'error',
        'errors' => [
          'password' => "Password is invalid!",
        ]
      ]),
      AppException::BAD_REQUEST
    );
  }
}
