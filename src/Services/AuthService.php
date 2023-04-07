<?php

declare(strict_types=1);

namespace Elfas\Services;

use Elfas\DB\Models\User;

class AuthService
{
  private const STATUS_SUCCESS = 'success';

  public function sendMsgUserCreated(User $user)
  {
    http_response_code(201);

    echo json_encode([
      'status' => self::STATUS_SUCCESS,
      'message' => 'The user has been successfully created',
      'user' => $user->response()
    ]);
  }

  public function sendMsgUserUpdated(User $user)
  {
    http_response_code(200);

    echo json_encode([
      'status' => self::STATUS_SUCCESS,
      'message' => 'The user has been successfully updated',
      'user' => $user->response()
    ]);
  }

  public function sendMsgUserDeleted(User $user)
  {
    http_response_code(204);
    /*
    echo json_encode([
      'status' => self::STATUS_SUCCESS,
      'message' => 'The user has been successfully deleted',
      'user' => $user->response()
    ]);*/
  }

  public function sendMsgUserGot(User $user)
  {
    http_response_code(200);

    echo json_encode([
      'status' => self::STATUS_SUCCESS,
      'user' => $user->response()
    ]);
  }
}
