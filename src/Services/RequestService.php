<?php

declare(strict_types=1);

namespace Elfas\Services;

class RequestService
{
  public function getData(): array
  {
    $req = json_decode(file_get_contents('php://input'));
    $data = [];
    foreach ($req as $field => $valueField) {
      $data[$field] = str_replace(" ", "", $valueField);
    }

    return $data;
  }
}
