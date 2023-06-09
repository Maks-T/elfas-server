<?php

declare(strict_types=1);

namespace Elfas\DB\Repositories;

use Elfas\DB\Models\Ques;
use Elfas\DB\DataBase\JsonDB;

class QuesRepository
{
  const DIR_PATH = STORE . 'DataUsers/';

  private JsonDb $jsonDB;

  private ?string $curUserId = null;

  public function __construct()
  {
  }

  public function setUser(string $userId): QuesRepository
  {
    if ($this->curUserId !== $userId) {
      if (!is_dir(self::DIR_PATH . $userId)) {
        mkdir(self::DIR_PATH . $userId, 0777, true);
      }

      $this->jsonDB = new JsonDB(self::DIR_PATH . $userId . '/questions.json', Ques::class);
      $this->curUserId = $userId;
    }
    return $this;
  }


  /**
   * @param Ques[] $questions
   * @return Ques[] array
   */
  public function createQuestions(string $userId, array $questions): ?array
  {
    $this->setUser($userId);

    if ($this->jsonDB->createItems($questions)) {
      return $questions;
    }

    return null;
  }

  /**
   * @param string $userId
   * @return Ques[]|null
   */
  public function getAllQuestions(string $userId): ?array
  {
    $this->setUser($userId);

    return $this->jsonDB->getAll();
  }

  public function getQuestions(string $userId, int $count, int $start): ?array
  {
    $this->setUser($userId);

    return $this->jsonDB->getItems($count, $start);
  }

  public function getQuestionById(string $id, string $userId): ?Ques
  {
    $this->setUser($userId);

    return $this->jsonDB->getByField('id', $id);
  }

  public function updateQuestionById(string $id, string $userId, Ques $question): ?Ques
  {
    $this->setUser($userId);

    return $this->jsonDB->updateByField('id', $id, $question);
  }

  public function deleteQuestionById(string $id, string $userId): ?Ques
  {
    $this->setUser($userId);

    return $this->jsonDB->deleteByField('id', $id);
  }
}
