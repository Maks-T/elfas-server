<?php

declare(strict_types=1);

namespace Elfas\DB\Repositories;

use Elfas\DB\DataBase\JsonDB;
use Elfas\DB\Models\Auth;
use Elfas\DB\Models\AuthConnection;

class AuthRepository
{
  const FILE_PATH = STORE . 'auth.json';

  private JsonDb $jsonDB;

  public function __construct()
  {
    $this->jsonDB = new JsonDB(self::FILE_PATH, Auth::class);
  }

  public function getPublicKey(string $userId, string $clientKey): string
  {

    /** @var Auth $findAuth */
    $findAuth = $this->jsonDB->getByField('userId', $userId);

    if ($findAuth) {

      foreach ($findAuth->connections as $connection) {

        if ($connection->clientKey === $clientKey) {
          $connection->lastConnect = date("F j, Y, g:i:s a");
          $this->jsonDB->updateByField('userId', $userId, $findAuth);

          return $connection->publicKey;
        }
      }

      $authConnection = new AuthConnection($clientKey);
      $findAuth->connections[] = $authConnection;
      $this->jsonDB->updateByField('userId', $userId, $findAuth);
      return $authConnection->publicKey;
    }

    $authConnection = new AuthConnection($clientKey);
    $auth = new Auth(['userId' => $userId]);
    $auth->connections[] = $authConnection;
    $this->jsonDB->create($auth);

    return $authConnection->publicKey;
  }

}
