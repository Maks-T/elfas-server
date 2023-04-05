<?php

declare(strict_types=1);

namespace Elfas\DataBase;

use Elfas\Exceptions\AppException;

class JsonDB
{
  const ERROR_SAVE_FILE = 'Unexpected error when saving a file';

  private string $path;

  /** @var object[] $items */
  private array $items = [];

  public function __construct(string $path, string $class)
  {
    $this->path = $path;
    $json = file_get_contents($this->path);
    $itemsJson = json_decode($json) ?? [];

    foreach ($itemsJson as $itemJson) {
      $this->items[] = new $class((array)$itemJson);
    }
  }

  public function create(object $item): ?object
  {

    $this->items[] = $item;
    $this->saveItemsToFile(__METHOD__);

    return $item;
  }

  public function getByField(string $field, string $value): ?object
  {
    foreach ($this->items as $itemFind) {
      if (isset($itemFind->$field)) {
        if ($itemFind->$field == $value) {

          return $itemFind;
        }
      }
    }

    return null;
  }

  public function updateByField(string $field, string $value, object $item): ?object
  {

    foreach ($this->items as $index => $itemFind) {
      if (isset($itemFind->$field)) {
        if ($itemFind->$field == $value) {

          $this->items[$index] = $item;
        }
      }
    }
    $this->saveItemsToFile(__METHOD__);

    return $item ?? null;
  }

  public function deleteByField(string $field, string $value): ?object
  {


    foreach ($this->items as $index => $itemFind) {
      if (isset($itemFind->$field)) {
        if ($itemFind->$field == $value) {
          $item = $itemFind;
          array_splice($this->items, $index, 1);
        }
      }
    }

    $this->saveItemsToFile(__METHOD__);

    return $item ?? null;
  }

  public function saveItemsToFile(string $methodCustomer)
  {
    try {
      $json = json_encode($this->items);
      file_put_contents($this->path, $json);
    } catch (\Throwable $e) {
      AppException::ThrowInternalServerError(self::ERROR_SAVE_FILE, $methodCustomer);
    }
  }
}
