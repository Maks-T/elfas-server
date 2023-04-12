<?php

declare(strict_types=1);

namespace Elfas\DB\DataBase;

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

  public function create(object $item): object
  {

    $this->items[] = $item;
    $this->saveItemsToFile(__METHOD__);

    return $item;
  }

  /**
   * @param object[] $items
   * @return object[]
   */
  public function createItems(array $items): array
  {

    $this->items[] = [...$this->items, ...$items];
    $this->saveItemsToFile(__METHOD__);

    return $items;
  }

  public function getSize(): int
  {
    return count($this->items);
  }

  public function getAll(): ?array
  {

    return $this->items;
  }

  public function getItems(int $count, int $start = 0): ?array
  {
    $length = count($this->items);

    if ($count > $length - $start) {
      $count = $length - $start;
    }
    return $count > 0 && $length > 0 ? array_slice($this->items, $start, $count) : [];
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
          $this->saveItemsToFile(__METHOD__);
          return $item;
        }
      }
    }

    return null;
  }

  public function deleteByField(string $field, string $value): ?object
  {

    foreach ($this->items as $index => $itemFind) {
      if (isset($itemFind->$field)) {
        if ($itemFind->$field == $value) {
          $item = $itemFind;
          array_splice($this->items, $index, 1);
          $this->saveItemsToFile(__METHOD__);
          return $item;
        }
      }
    }

    return null;
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
