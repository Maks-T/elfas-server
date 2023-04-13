<?php

declare(strict_types=1);

namespace Elfas\Services;

use Elfas\DB\Models\Ques;

class QuesService
{
  private const STATUS_SUCCESS = 'success';

  /**
   * @param Ques[] $questions
   * @return void
   */
  public function sendMsgQuestionsCreated(array $questions): void
  {
    http_response_code(201);

    echo json_encode([
      'status' => self::STATUS_SUCCESS,
      'message' => 'The questions have been successfully created',
      'questions' => $questions
    ]);
  }

  public function sendMsgQuestionUpdated(Ques $question)
  {
    http_response_code(200);

    echo json_encode([
      'status' => self::STATUS_SUCCESS,
      'message' => 'The question has been successfully updated',
      'question' => $question
    ]);
  }

  public function sendMsgquestionDeleted(Ques $question)
  {
    http_response_code(204);
  }

  /**
   * @param Ques[] $questions
   * @return void
   */
  public function sendMsgQuestionsGot($questions)
  {
    http_response_code(200);

    echo json_encode([
      'status' => self::STATUS_SUCCESS,
      'questions' => $questions
    ]);
  }
}
