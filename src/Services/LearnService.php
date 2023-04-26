<?php

declare(strict_types=1);

namespace Elfas\Services;

use Elfas\DB\Models\Ques;

class LearnService
{
  private const STATUS_SUCCESS = 'success';

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
  public function sendMsgQuestionsGot($questions, $learnQuestions)
  {
    http_response_code(200);

    echo json_encode([
      'status' => self::STATUS_SUCCESS,
      'questions' => [...$questions, ...$learnQuestions]
    ]);
  }
}
