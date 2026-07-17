<?php

namespace App\Destiny;

/**
 * Wraps a public vendor payload and exposes sale item groupings.
 */
class Vendor
{
    public $refreshDate;

    private array $saleItems = [];

    /**
     * Build a vendor wrapper from the public vendors payload.
     */
    public function __construct(int|string $iHash, mixed $oData)
    {
        if ($oData) {
            if (isset($oData->vendors->data->{$iHash}->nextRefreshDate)) {
                $this->refreshDate = $oData->vendors->data->{$iHash}->nextRefreshDate;
            }

            if (isset($oData->sales->data->{$iHash}->saleItems)) {
                $this->saleItems = $oData->sales->data->{$iHash}->saleItems;
            }
        }
    }

    /**
     * Get grouped sale items, optionally filtered to a single group.
     */
    public function getSales(string|false $strFilter = false): array
    {
        $aWeapons = [];
        $aHelmets = [];
        $aGauntlets = [];
        $aChests = [];
        $aLegs = [];
        $aConsumables = [];

        foreach ($this->saleItems as $oSaleItem) {
            $oItem = new EquipmentItem($oSaleItem);
            $oItem->load($oSaleItem, [], false);

            if ($oItem && isset($oItem->tierTypeHash) && $oItem->tierTypeHash === 2759499571) { // Exotics only
                switch ($oItem->bucketTypeHash) {
                    // Consumables
                    case 1469714392:
                        $aConsumables[] = $oItem;
                        break;

                        // Weapons
                    case 1498876634:
                    case 2465295065:
                    case 953998645:
                        $aWeapons[] = $oItem;
                        break;

                        // Gear
                    case 3448274439:
                        $aHelmets[] = $oItem;
                        break;
                    case 3551918588:
                        $aGauntlets[] = $oItem;
                        break;
                    case 14239492:
                        $aChests[] = $oItem;
                        break;
                    case 20886954:
                        $aLegs[] = $oItem;
                        break;
                }
            }
        }

        $aReturn = [
            'weapons' => $aWeapons,
            'helmets' => $aHelmets,
            'gauntlets' => $aGauntlets,
            'chests' => $aChests,
            'legs' => $aLegs,
            'consumables' => $aConsumables,
        ];

        return $strFilter ? $aReturn[$strFilter] : $aReturn;
    }
}
