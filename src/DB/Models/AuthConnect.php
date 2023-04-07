<?php

declare(strict_types=1);

namespace Elfas\DB\Models;

class AuthConnect
{
  public string $publicKey;

  public string $platform;

  public string $lastConnect;

  public function __construct()
  {
    $this->publicKey = gen_uuid();
    $this->platform = $_SERVER['HTTP_SEC_CH_UA_PLATFORM'];
    $this->lastConnect = date("F j, Y, g:i a");
  }
}
