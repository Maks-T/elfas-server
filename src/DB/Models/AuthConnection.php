<?php

declare(strict_types=1);

namespace Elfas\DB\Models;

class AuthConnection
{
  public string $publicKey;

  public string $clientKey;

  public string $lastConnect;

  public function __construct(array|object $connectionData)
  {
    $this->publicKey = $connectionData['publicKey'] ?? gen_uuid();
    $this->clientKey = $connectionData['clientKey'];
    $this->lastConnect = $connectionData['lastConnect'] ?? date("F j, Y, g:i:s a");
  }

  public function changePublicKey(): string
  {
    $this->publicKey = gen_uuid();

    return  $this->publicKey;
  }

  public function updateLastDate(): string
  {
    $this->lastConnect = date("F j, Y, g:i:s a");

    return  $this->lastConnect;
  }
}
