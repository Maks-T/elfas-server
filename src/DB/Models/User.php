<?php

declare(strict_types=1);

namespace Elfas\DB\Models;

class User
{
  public string $id;
  public string $login;
  public string $password;
  public string $email;
  public string $name;

  public function __construct(array $userData)
  {

    $this->id =
      !isset($userData['id'])
      ?  uniqid()
      : $userData['id'];

    $this->login = $userData['login'];
    $this->password = $userData['password'];
    $this->email = $userData['email'];
    $this->name = $userData['name'];
  }

  public function response()
  {
    return [
      'id' => $this->id,
      'login' => $this->login,
      'email' => $this->email,
      'name' => $this->name
    ];
  }
}
