<?php

declare(strict_types=1);

namespace Elfas\DB\Models;

class AuthConnection
{
  public string $publicKey;

  public string $clientKey;

  public string $lastConnect;

  public function __construct(string $clientKey)
  {
    $this->publicKey = gen_uuid();
    $this->clientKey = $clientKey;
    $this->lastConnect = date("F j, Y, g:i:s a");
  }
}
