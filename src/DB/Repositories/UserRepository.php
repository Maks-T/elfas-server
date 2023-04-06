<?php

declare(strict_types=1);

namespace Elfas\DB\Repositories;

use Elfas\DB\Models\User;
use Elfas\DB\DataBase\JsonDB;

class UserRepository
{
  const FILE_PATH =  STORE . 'users.json';

  private JsonDb $jsonDB;

  public function __construct()
  {
    $this->jsonDB = new JsonDB(self::FILE_PATH, User::class);
  }

  public function createUser(User $user): ?User
  {
    $findUser = $this->getUserByLogin($user->login);

    if (!$findUser) {
      return $this->jsonDB->create($user);
    }

    return null;
  }

  public function getUserById(string $id): ?User
  {
    return $this->jsonDB->getByField('id', $id);
  }


  public function getUserByLogin(string $login): ?User
  {
    return $this->jsonDB->getByField('login', $login);
  }

  public function getUserByEmail(string $email): ?User
  {
    return $this->jsonDB->getByField('email', $email);
  }

  public function updateUserById(string $id, User $user): ?User
  {
    $findUser = $this->getUserById($id);

    if ($findUser) {

      foreach ((array)$findUser as $prop => $value) {
        echo '$prop = ' . $prop . ' $value = ' . $value . '<br>';
      }
      $user->name ?? $user->name = $findUser->name;
      $user->login ?? $user->login = $findUser->login;
      $user->email ?? $user->email = $findUser->email;
      $user->password ?? $user->email = $findUser->password;
      $user->id = $findUser->id;

      return $this->jsonDB->updateByField('id', $id, $user);
    }

    return null;
  }

  public function deleteUserById(string $id): ?User
  {
    return $this->jsonDB->deleteByField('id', $id);
  }
}
