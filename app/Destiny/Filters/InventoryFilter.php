<?php

namespace App\Destiny\Filters;

use App\Destiny\EquipmentItem;
use App\Enums\InventoryBucket;

/**
 * Filters and formats profile inventory data into command-ready items.
 */
class InventoryFilter
{
    private array $items = [];

    private object $instances;

    private object $sockets;

    /**
     * Build an inventory filter from raw profile inventory component data.
     */
    public function __construct(array $aInventoryItems, object $aInstances, object $aSockets)
    {
        if (! empty($aInventoryItems)) {
            foreach ($aInventoryItems as $oInventoryItem) {
                $this->items[$oInventoryItem->bucketHash] = $oInventoryItem;
            }
        }

        $this->instances = $aInstances;
        $this->sockets = $aSockets;
    }

    /**
     * Resolve one or more requested items from the character inventory.
     */
    public function getItems(array|string $aSearchItems, bool $bPerks = false): array|EquipmentItem|false
    {
        $bArray = true;
        if (! is_array($aSearchItems)) {
            $aSearchItems = [$aSearchItems];
            $bArray = false;
        }

        $a = [];
        foreach ($aSearchItems as $strSearchItem) {
            $bucketHash = $this->bucketHashFor($strSearchItem);
            $oItem = false;
            if (isset($this->items[$bucketHash])) {
                $oItem = new EquipmentItem($this->items[$bucketHash]);
                $oItem->load(
                    $this->instances->{$oItem->itemInstanceId},
                    $this->sockets->{$oItem->itemInstanceId}->sockets ?? [],
                    $bPerks
                );
            }

            $a[$bucketHash] = $oItem;
        }

        return $bArray === false ? $a[$bucketHash] : $a;
    }

    /**
     * Resolve a command key to its inventory bucket hash.
     */
    private function bucketHashFor(string $strSearchItem): int
    {
        return InventoryBucket::from($strSearchItem)->hash();
    }
}
