<?php

declare(strict_types=1);

namespace Elfas\DB\Models;

class Auth
{
  public string $userId;

  /** @var AuthConnection[] $connections */
  public array $connections = [];

  public function __construct(array $authData)
  {
    $this->userId = $authData['userId'];

    if (array_key_exists('connections', $authData)) {
      foreach ((array)$authData['connections'] as $connection) {
        $this->connections[] = new AuthConnection((array)$connection);
      }
    }

  }

  public function createConnection(string $clientKey): AuthConnection
  {
    $clientKey = password_hash($clientKey, PASSWORD_DEFAULT);

    $connection = new AuthConnection(['clientKey' => $clientKey]);
    $this->connections[] = $connection;

    return $connection;
  }

  public function getPublicKey(string $clientKey): ?string
  {
    foreach ($this->connections as $connection) {


      if (password_verify($clientKey, $connection->clientKey)) {
        $connection->updateLastDate();
        $connection->changePublicKey();

        return $connection->publicKey;
      }

    }

    return null;
  }

  public function checkKeys(string $clientKey, string $publicKey, bool $updatePublicKey = true): ?string
  {
    foreach ($this->connections as $connection) {

      if (password_verify($clientKey, $connection->clientKey) && $connection->publicKey === $publicKey) {

        $connection->updateLastDate();

        if ($updatePublicKey) {
          $connection->changePublicKey();
        }

        return $connection->publicKey;
      }
    }

    return null;
  }

}
