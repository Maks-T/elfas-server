<?php

declare(strict_types=1);

namespace Elfas\DB\Repositories;

use Elfas\DB\Models\Learn;
use Elfas\DB\DataBase\JsonDB;

class LearnRepository
{
  const DIR_PATH = STORE . 'DataUsers/';

  const FILE_NAME = '/learn.json';

  private JsonDb $jsonDB;

  private ?string $curUserId = null;

  public function __construct()
  {
  }

  public function setUser(string $userId): LearnRepository
  {
    if ($this->curUserId !== $userId) {
      if (!is_dir(self::DIR_PATH . $userId)) {
        mkdir(self::DIR_PATH . $userId, 0777, true);
      }

      $this->jsonDB = new JsonDB(self::DIR_PATH . $userId . self::FILE_NAME, Learn::class);
      $this->curUserId = $userId;
    }

    return $this;
  }


  /**
   * @param string $userId
   * @param array $learnQuestions
   * @return array|null array
   */
  public function createLearnQuestions(string $userId, array $learnQuestions): ?array
  {
    $this->setUser($userId);

    if ($this->jsonDB->createItems($learnQuestions)) {
      return $learnQuestions;
    }

    return null;
  }

  /**
   * @param string $userId
   * @return Learn[]|null
   */
  public function getAllLearnQuestions(string $userId): ?array
  {
    $this->setUser($userId);

    return $this->jsonDB->getAll();
  }

  /**
   * @param string $userId
   * @param int $count
   * @param int $start
   * @return Learn[]|null
   */
  public function getLearnQuestions(string $userId, int $count, int $start): ?array
  {
    $this->setUser($userId);

    return $this->jsonDB->getItems($count, $start);
  }

  public function getLearnQuestionById(string $id, string $userId): ?Learn
  {
    $this->setUser($userId);

    return $this->jsonDB->getByField('id', $id);
  }

  public function updateLearnQuestionById(string $id, string $userId, Learn $learnQuestion): ?Learn
  {
    $this->setUser($userId);

    return $this->jsonDB->updateByField('id', $id, $learnQuestion);
  }

  public function deleteLearnQuestionById(string $id, string $userId): ?Learn
  {
    $this->setUser($userId);

    return $this->jsonDB->deleteByField('id', $id);
  }
}
