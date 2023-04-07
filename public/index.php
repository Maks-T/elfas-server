<?php

declare(strict_types=1);

require './../src/init.php';

use Elfas\App;
use Elfas\Controllers\UserController;
use Elfas\Controllers\AuthController;
use Elfas\Exceptions\AppException;
use Elfas\Router;


$_SERVER['REQUEST_URI'] = str_replace('/elfas-server', '', $_SERVER['REQUEST_URI']);

new AppException(); //creating an instance of an error handler

new App();

$router = new Router();

$router->get('/user', [UserController::class, 'get', 'application/json']);
$router->post('/user', [UserController::class, 'create', 'application/json']);
$router->put('/user', [UserController::class, 'update', 'application/json']);
$router->delete('/user', [UserController::class, 'delete', 'application/json']);

$router->post('/auth', [AuthController::class, 'login', 'application/json']);

$router->resolve($_SERVER['REQUEST_URI'], strtolower($_SERVER['REQUEST_METHOD']));
