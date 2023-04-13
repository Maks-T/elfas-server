<?php

declare(strict_types=1);

namespace Elfas\DB\Models;

class Ques
{
  public string $id;

  public ?string $parentUserId = null;

  public string $en;

  /** @var string[] $ru */
  public array $ru;

  /** @var string|null $tr transcription */
  public ?string $tr = null;

  public ?string $audioSrc = null;

  public ?string $level = null;

  public function __construct(array $quesData)
  {

    $this->id =
      !isset($quesData['id'])
      ?  uniqid()
      : $quesData['id'];

    $this->parentUserId = $quesData['parenUserId'] ?? null;
    $this->en = $quesData['en'];
    $this->ru = $quesData['ru'];
    $this->tr = $quesData['tr'] ?? null;
    $this->audioSrc = $quesData['audioSrc'] ?? null;
    $this->audioSrc = $quesData['level'] ?? null;
  }
}
