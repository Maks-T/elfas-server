<?php

declare(strict_types=1);

namespace Elfas\DB\Repositories;

use Elfas\DB\DataBase\JsonDB;
use Elfas\DB\Models\Auth;

class AuthRepository
{
  const FILE_PATH = STORE . 'auth.json';

  private JsonDb $jsonDB;

  public function __construct()
  {
    $this->jsonDB = new JsonDB(self::FILE_PATH, Auth::class);
  }

  public function deleteByUserId(string $userId)
  {
    $this->jsonDB->deleteByField('userId', $userId);
  }

  public function getPublicKey(string $userId, string $clientKey): string
  {

    /** @var Auth $findAuth */
    $findAuth = $this->jsonDB->getByField('userId', $userId);

    if ($findAuth) {

      $publicKey = $findAuth->getPublicKey($clientKey);

      if ($publicKey) {
        $this->jsonDB->updateByField('userId', $findAuth->userId, $findAuth);

        return $publicKey;
      }

      $authConnection = $findAuth->createConnection($clientKey);
      $this->jsonDB->updateByField('userId', $userId, $findAuth);
      return $authConnection->publicKey;
    }


    $auth = new Auth(['userId' => $userId]);
    $authConnection = $auth->createConnection($clientKey);
    $this->jsonDB->create($auth);

    return $authConnection->publicKey;
  }

  public function checkKeys(string $userId, string $clientKey, string $publicKey, bool $updatePublicKey = true): ?string
  {
    /** @var Auth $findAuth */
    $findAuth = $this->jsonDB->getByField('userId', $userId);

    if ($findAuth) {

      $publicKey = $findAuth->checkKeys($clientKey, $publicKey, $updatePublicKey);

      if ($publicKey) {
        $this->jsonDB->updateByField('userId', $findAuth->userId, $findAuth);

        return $publicKey;
      }

    }
    return null;
  }

}
