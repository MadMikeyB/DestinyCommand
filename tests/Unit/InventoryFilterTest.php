<?php

namespace Tests\Unit;

use App\Destiny\Filters\InventoryFilter;
use PHPUnit\Framework\TestCase;

class InventoryFilterTest extends TestCase
{
    public function test_it_uses_enum_backed_hashes_for_single_item_lookup(): void
    {
        $filter = new InventoryFilter([], (object) [], (object) []);

        $item = $filter->getItems('primary');

        $this->assertFalse($item);
    }

    public function test_it_uses_enum_backed_hashes_for_multiple_item_lookup(): void
    {
        $filter = new InventoryFilter([], (object) [], (object) []);

        $items = $filter->getItems(['primary', 'ship']);

        $this->assertSame([
            1498876634 => false,
            284967655 => false,
        ], $items);
    }
}
