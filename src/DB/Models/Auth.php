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
    isset($authData['connections']) ? $this->connections = (array)$authData['connections'] : [];
  }
}
