<?php

namespace CQRS\Helpers;

/**
 * Helper providing \Traversable and \Countable implementation.
 */
trait InventoryTrait
{
    private int $position = 0;
    private array $inventory = [];

    public function add(string $className)
    {
        $this->inventory[] = $className;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->inventory[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid()
    {
        return isset($this->inventory[$this->position]);
    }

    public function count()
    {
        return \count($this->inventory);
    }
}
