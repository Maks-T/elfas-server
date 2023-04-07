<?php

declare(strict_types=1);

namespace Elfas\DB\Models;

class Auth
{
  public string $userId;

  public AuthConnect $connections = [];

  public function __construct(string $userId)
  {
    $this->$userId = $userId;  
  }

  public function response()
  {
  }
}
