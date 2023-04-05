<?php

declare(strict_types=1);

namespace Elfas;

use Elfas\Exceptions\AppException;

class Router
{
  private array $routes = [];

  public function register(string $requestMethod, string $route, array $action): self
  {

    $this->routes[$requestMethod][$route] = $action;

    return $this;
  }

  public function get(string $route, array $action): self
  {
    $this->register('get', $route, $action);

    return $this;
  }

  public function post(string $route, array $action): self
  {
    $this->register('post', $route, $action);

    return $this;
  }

  public function put(string $route, array $action): self
  {
    $this->register('put', $route, $action);

    return $this;
  }

  public function delete(string $route, array $action): self
  {
    $this->register('delete', $route, $action);

    return $this;
  }

  public function resolve(string $requestUri, string $requestMethod)
  {
    $route = explode('?', $requestUri)[0];
echo  $requestUri;
    echo  $requestMethod;
      echo json_encode($this->routes);

    $action = $this->routes[$requestMethod][$route] ?? null;

    if (!$action) {
      AppException::ThrowRouteNotFound("The endpoint $action does not exist", __METHOD__);
    }

    [$class, $method, $contentType] = $action;

    if (class_exists($class)) {
      $class = new $class;

      header("Content-Type: $contentType");

      if (method_exists($class, $method)) {

        return call_user_func_array([$class, $method], []);
      }
    }

    AppException::ThrowRouteNotFound("The method $method does not exist in class $class", __METHOD__);
  }
}
