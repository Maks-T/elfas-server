<?php

declare(strict_types=1);

define('ROOT', __DIR__);
define('STORE', ROOT . '/Store/');

spl_autoload_register(function ($class) {

  $filePath = ROOT . str_replace('Elfas\\', '/', $class) . '.php';
  echo '<code>Подключение класса: ' . $filePath . ' ... </code><br>';

  if (is_file($filePath)) {
    require_once $filePath;
  }
});
