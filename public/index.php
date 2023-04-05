<?php

declare(strict_types=1);

require './../src/init.php';

use Elfas\App;
use Elfas\Exceptions\AppException;

new AppException(); //creating an instance of an error handler


try {
  throw new Error();
  new App();
} catch (\Throwable $e) {

  AppException::ThrowInternalServerError();
}
