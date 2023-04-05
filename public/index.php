<?php

declare(strict_types=1);

require './../src/init.php';

use Elfas\App;
use Elfas\Exceptions\AppException;
use Elfas\Router;

new AppException(); //creating an instance of an error handler

try {
  new App();

  $router = new Router();

  $router->get('/user', [UserController::class, 'get', 'application/json']);
  $router->post('/user', [UserController::class, 'create', 'application/json']);
  $router->put('/user', [UserController::class, 'update', 'application/json']);
  $router->delete('/user', [UserController::class, 'delete', 'application/json']);
} catch (\Throwable $e) {

  AppException::ThrowInternalServerError();
}
