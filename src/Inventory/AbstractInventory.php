<?php

namespace CQRS\Inventory;

use CQRS\Helpers\InventoryTrait;

abstract class AbstractInventory implements \Iterator, \Countable
{
    use InventoryTrait;
}
