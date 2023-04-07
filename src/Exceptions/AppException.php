<?php

namespace Elfas\Exceptions;

class AppException
{
  const ROURE_NOT_FOUND = 1;
  const BAD_REQUEST = 400;
  const NOT_FOUND = 404;
  const INTERNAL_SERVER_ERROR = 500;
  const SERVICE_UNAVIALABLE = 503;

  private const STATUS_FATAL = 'fatal';
  private const STATUS_ERROR = 'error';

  public function __construct()
  {
    set_exception_handler(array($this, 'exception_handler'));
  }

  public function exception_handler($e): void
  {
    switch ($e->getCode()) {
      case self::ROURE_NOT_FOUND:
      case self::BAD_REQUEST:
      case self::NOT_FOUND:
      case self::INTERNAL_SERVER_ERROR:
      case self::SERVICE_UNAVIALABLE:
      default:
        $this->sendMessage($e);
        break;
    }
  }

  private function sendMessage(\Exception | \Error $e): void
  {
    http_response_code($e->getCode());
    echo $e->getMessage();
  }

  public static function ThrowRouteNotFound($message = 'Unknown server error', $methodCustomer = 'unknown method')
  {

    throw new \Exception(
      json_encode(['status' => self::STATUS_ERROR, 'message' => $message, 'method' => $methodCustomer]),
      AppException::ROURE_NOT_FOUND
    );
  }

  public static function ThrowBadRequest($message = 'Unknown server error', $methodCustomer = 'unknown method')
  {

    throw new \Exception(
      json_encode(['status' => self::STATUS_ERROR, 'message' => $message, 'method' => $methodCustomer]),
      AppException::BAD_REQUEST
    );
  }

  public static function ThrowResourceNotFound($message = 'Unknown server error', $methodCustomer = 'unknown method')
  {

    throw new \Exception(
      json_encode(['status' => self::STATUS_ERROR, 'message' => $message, 'method' => $methodCustomer]),
      AppException::NOT_FOUND
    );
  }

  public static function ThrowServiceUnavailable($message = 'Unknown server error', $methodCustomer = 'unknown method')
  {

    throw new \Exception(
      json_encode(['status' => self::STATUS_ERROR, 'message' => $message, 'method' => $methodCustomer]),
      AppException::SERVICE_UNAVIALABLE
    );
  }
  public static function ThrowInternalServerError($message = 'Unknown server error', $methodCustomer = 'unknown method')
  {

    throw new \Exception(
      json_encode(['status' => self::STATUS_FATAL, 'message' => $message, 'method' => $methodCustomer]),
      AppException::INTERNAL_SERVER_ERROR
    );
  }
}
