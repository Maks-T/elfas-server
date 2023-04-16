<?php

declare(strict_types=1);

namespace Elfas\DB\Models;


class Learn
{
  public string $id;

  public int $dayN;

  public string $dateRevise;

  public string $progress;

  public function __construct(array $learnData)
  {

    $this->id = $learnData['id'];
    $this->dayN = $learnData['dayN'] ?? 0;
    // y = 2x + 1
    $dayY = 2 * $this->dayN + 1;
    $this->dateRevise = $learnData['dateRevise'] ?? date('F j, Y, g:i a',
      time() + $dayY * 86400);
    $this->progress = $learnData['progress'] ?? '';

  }

}
