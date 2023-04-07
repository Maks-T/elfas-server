<?php

declare(strict_types=1);

define('ROOT', __DIR__);
define('STORE', ROOT . '/Store/');

require_once ROOT . '/libs/index.php';

$_ENV['PSW_SECRET'] = 'TSATSURA';

spl_autoload_register(function ($class) {

  $filePath = ROOT . str_replace('Elfas\\', '/', $class) . '.php';

  if (is_file($filePath)) {
    require_once $filePath;
  }
});
