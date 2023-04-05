<?php

namespace Elfas\Exceptions;

class AppException
{
  const ROURE_NOT_FOUND = 1;
  const BAD_REQUEST = 400;
  const NOT_FOUND = 404;
  const INTERNAL_SERVER_ERROR = 500;

  private const STATUS_FATAL = 'fatal';

  public function __construct()
  {
    set_exception_handler(array($this, 'exception_handler'));
  }

  public function exception_handler($e)
  {
    switch ($e->getCode()) {
      case self::ROURE_NOT_FOUND:

        break;
      case self::BAD_REQUEST:
      case self::NOT_FOUND:
      case self::INTERNAL_SERVER_ERROR:
      default:
        $this->sendMessage($e);
        break;
    }
  }

  private function sendMessage(\Exception $e)
  {
    http_response_code($e->getCode());
    echo $e->getMessage();
  }

  public static function ThrowInternalServerError($message = 'Unknown server error', $methodCustomer = 'unknown method')
  {

    return throw new \Exception(
      json_encode(['status' => self::STATUS_FATAL, 'message' => $message, 'method' => $methodCustomer]),
      AppException::INTERNAL_SERVER_ERROR
    );
  }
}