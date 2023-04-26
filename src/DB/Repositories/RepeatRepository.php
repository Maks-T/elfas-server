<?php

declare(strict_types=1);

namespace Elfas\DB\Repositories;

use Elfas\DB\Models\Learn;
use Elfas\DB\DataBase\JsonDB;

class RepeatRepository extends LearnRepository
{

  protected string $fileName = '/repeat.json';

  public function __construct()
  {
    $this->sortFn = fn(Learn $a, Learn $b) => strtotime($a->dateRevise) - strtotime($b->dateRevise);
  }

  public function 

}
