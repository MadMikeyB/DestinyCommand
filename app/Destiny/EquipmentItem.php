<?php

namespace App\Destiny;

/**
 * Represents a formatted Destiny inventory or vendor item.
 */
class EquipmentItem
{
    private const DISPLAYED_ARMOR_STATS = [
        2996146975 => 'Weapons',
        392767087 => 'Health',
        1943323491 => 'Class',
        1735777505 => 'Grenade',
        144602215 => 'Super',
        4244567218 => 'Melee',
    ];

    private const HIDDEN_PLUG_NAMES = [
        'Empty Artifact Mod',
        'Upgrade Armor',
    ];

    /**
     * Build an equipment item shell from a raw item payload.
     */
    public function __construct($oEquipmentItem)
    {
        $this->itemInstanceId = $oEquipmentItem->itemInstanceId ?? 0;
        $this->itemHash = $oEquipmentItem->itemHash;
    }

    /**
     * Hydrate manifest-backed item details and optional perks.
     */
    public function load($oItemInstance, $aSockets, $bPerks, bool $includeCosmetics = false, ?object $oStats = null, ?array $setData = null, ?Manifest $manifest = null)
    {
        $oManifest = $manifest ?? new Manifest;
        $oItem = $oManifest->getDefinition('InventoryItem', $this->itemHash);

        $this->name = $oItem->displayProperties->name;
        $this->bucketTypeHash = $oItem->inventory->bucketTypeHash ?? 0;
        $this->light = $oItemInstance->primaryStat->value ?? 0;
        $this->quantity = $oItemInstance->quantity ?? 1;
        $this->tierTypeHash = $oItem->inventory->tierTypeHash ?? 0;
        $this->itemTypeDisplayName = $oItem->itemTypeDisplayName ?? null;

        if ($oStats !== null) {
            $this->stats = $this->resolveStats($oStats);
        }

        if (empty($this->stats) && ! empty($aSockets)) {
            $this->stats = $this->resolveSocketStats($oManifest, $aSockets);
        }

        if ($setData !== null) {
            $this->setBonuses = $setData;
        }

        if ($bPerks && ! $oItem->redacted) {
            if (! empty($aSockets)) {
                foreach ($aSockets as $oSocket) {
                    if ($oSocket->isEnabled && $oSocket->isVisible) {
                        $oPlug = $oManifest->getDefinition('InventoryItem', $oSocket->plugHash);

                        // Show progress if tracker is enabled
                        if (isset($oSocket->plugObjectives[0]) && $oSocket->plugObjectives[0]->visible) {
                            $oObjective = $oManifest->getDefinition('Objective', $oSocket->plugObjectives[0]->objectiveHash);
                            if (isset($oObjective->progressDescription) && trim($oObjective->progressDescription) != '') {
                                $oPlug->displayProperties->name .= ' ('.$oObjective->progressDescription.': '.$oSocket->plugObjectives[0]->progress.')';
                            }
                        }

                        // Show tier upgrade type
                        if ((strpos($oPlug->displayProperties->name, 'Tier ') !== false || $oPlug->displayProperties->name == 'Masterwork') && isset($oPlug->investmentStats[0])) {
                            $oStat = $oManifest->getDefinition('Stat', $oPlug->investmentStats[0]->statTypeHash);
                            if (isset($oStat->displayProperties->name)) {
                                if (strpos($oPlug->displayProperties->name, 'Tier ') !== false) {
                                    $oPlug->displayProperties->name = 'Tier '.$oPlug->investmentStats[0]->value;
                                }

                                $oPlug->displayProperties->name .= ' ('.$oStat->displayProperties->name.')';
                            }
                        }

                        // Only show perks + mods
                        if ($oPlug->inventory->bucketTypeHash == 1469714392 || $oPlug->inventory->bucketTypeHash == 3313201758 || $oPlug->inventory->bucketTypeHash == 2422292810) {
                            if ($this->shouldDisplayPlug($oPlug->displayProperties->name, $includeCosmetics)) {
                                $this->perks[] = $oPlug->displayProperties->name;
                            }
                        }
                    }
                }
            }
        }

        // Vendor items costs
        if (isset($oItemInstance->costs) && ! empty($oItemInstance->costs)) {
            $aCosts = [];
            foreach ($oItemInstance->costs as $oCost) {
                $oCostItem = new EquipmentItem($oCost);
                $oCostItem->load($oCost, [], false);
                unset($oCostItem->itemInstanceId, $oCostItem->bucketTypeHash, $oCostItem->light);
                $aCosts[] = $oCostItem;
            }
            $this->costs = $aCosts;
        }
    }

    /**
     * Resolve displayable armor stat values.
     */
    private function resolveStats(object $stats): array
    {
        $resolvedStats = [];

        foreach ($stats as $statData) {
            if (($statData->value ?? 0) <= 0) {
                continue;
            }

            $statName = self::DISPLAYED_ARMOR_STATS[$statData->statHash] ?? null;

            if ($statName === null) {
                continue;
            }

            $resolvedStats[$statName] = $statData->value;
        }

        return $resolvedStats;
    }

    /**
     * Resolve armor stats by summing enabled plug stat values from sockets.
     */
    private function resolveSocketStats(Manifest $manifest, array $sockets): array
    {
        $resolvedStats = [];

        foreach ($sockets as $socket) {
            if (! ($socket->isEnabled ?? false) || ! isset($socket->plugHash)) {
                continue;
            }

            $plugDefinition = $manifest->getDefinition('InventoryItem', $socket->plugHash);

            if ($plugDefinition === false || empty($plugDefinition->investmentStats)) {
                continue;
            }

            foreach ($plugDefinition->investmentStats as $investmentStat) {
                if (($investmentStat->value ?? 0) === 0) {
                    continue;
                }

                $statName = self::DISPLAYED_ARMOR_STATS[$investmentStat->statTypeHash] ?? null;

                if ($statName === null) {
                    continue;
                }

                $resolvedStats[$statName] = ($resolvedStats[$statName] ?? 0) + $investmentStat->value;
            }
        }

        return $resolvedStats;
    }

    /**
     * Filter out generic placeholder or cosmetic plugs from chat output.
     */
    private function shouldDisplayPlug(string $plugName, bool $includeCosmetics): bool
    {
        if (! $includeCosmetics && in_array($plugName, ['Default Shader', 'Default Ornament'], true)) {
            return false;
        }

        return ! in_array($plugName, self::HIDDEN_PLUG_NAMES, true);
    }
}
