<?php

namespace App\Destiny\Filters;

use App\Destiny\EquipmentItem;
use App\Destiny\Manifest;
use App\Enums\InventoryBucket;

/**
 * Filters and formats profile inventory data into command-ready items.
 */
class InventoryFilter
{
    private array $items = [];

    private object $instances;

    private object $sockets;

    private object $stats;

    private ?Manifest $manifest = null;

    private array $definitionCache = [];

    /**
     * Build an inventory filter from raw profile inventory component data.
     */
    public function __construct(array $aInventoryItems, object $aInstances, object $aSockets, object $aStats)
    {
        if (! empty($aInventoryItems)) {
            foreach ($aInventoryItems as $oInventoryItem) {
                $this->items[$oInventoryItem->bucketHash] = $oInventoryItem;
            }
        }

        $this->instances = $aInstances;
        $this->sockets = $aSockets;
        $this->stats = $aStats;
    }

    /**
     * Resolve one or more requested items from the character inventory.
     */
    public function getItems(array|string $aSearchItems, bool $bPerks = false, bool $includeCosmetics = false): array|EquipmentItem|false
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
                    $bPerks,
                    $includeCosmetics,
                    $this->stats->{$oItem->itemInstanceId}->stats ?? null,
                    $this->setDataFor($this->items[$bucketHash]),
                    $this->manifest(),
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

    /**
     * Resolve displayable set bonus data for an equipped item.
     *
     * @return array{name: string, equippedCount: int, bonuses: array<int, array{required: int, name: string}>}|null
     */
    private function setDataFor(object $inventoryItem): ?array
    {
        $itemDefinition = $this->getItemDefinition($inventoryItem->itemHash);

        if ($itemDefinition === false) {
            return null;
        }

        $setHash = $itemDefinition->equippingBlock->equipableItemSetHash ?? null;

        if (! $setHash) {
            return null;
        }

        $setDefinition = $this->manifest()->getDefinition('EquipableItemSet', $setHash);

        if ($setDefinition === false || empty($setDefinition->setPerks)) {
            return null;
        }

        $equippedCount = 0;
        foreach ($this->items as $equippedItem) {
            $equippedDefinition = $this->getItemDefinition($equippedItem->itemHash);

            if ($equippedDefinition === false) {
                continue;
            }

            if (($equippedDefinition->equippingBlock->equipableItemSetHash ?? null) === $setHash) {
                $equippedCount++;
            }
        }

        $bonuses = [];
        foreach ($setDefinition->setPerks as $setPerk) {
            $perkDefinition = $this->manifest()->getDefinition('SandboxPerk', $setPerk->sandboxPerkHash);

            if ($perkDefinition === false || empty($perkDefinition->displayProperties->name)) {
                continue;
            }

            $bonuses[] = [
                'required' => $setPerk->requiredSetCount,
                'name' => $perkDefinition->displayProperties->name,
            ];
        }

        if (empty($bonuses) || empty($setDefinition->displayProperties->name)) {
            return null;
        }

        return [
            'name' => $setDefinition->displayProperties->name,
            'equippedCount' => $equippedCount,
            'bonuses' => $bonuses,
        ];
    }

    /**
     * Resolve and cache an inventory item definition.
     */
    private function getItemDefinition(int|string $itemHash): object|false
    {
        if (! array_key_exists((string) $itemHash, $this->definitionCache)) {
            $this->definitionCache[(string) $itemHash] = $this->manifest()->getDefinition('InventoryItem', $itemHash);
        }

        return $this->definitionCache[(string) $itemHash];
    }

    /**
     * Lazily resolve the manifest to keep simple unit tests lightweight.
     */
    private function manifest(): Manifest
    {
        return $this->manifest ??= new Manifest;
    }
}
