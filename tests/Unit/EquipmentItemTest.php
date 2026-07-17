<?php

namespace Tests\Unit;

use App\Destiny\EquipmentItem;
use App\Destiny\Manifest;
use PHPUnit\Framework\TestCase;

class EquipmentItemTest extends TestCase
{
    public function test_it_can_resolve_armor_stats_from_enabled_socket_plugs_when_item_stats_are_missing(): void
    {
        $item = new EquipmentItem((object) [
            'itemInstanceId' => 'instance-1',
            'itemHash' => 1001,
        ]);

        $item->load(
            (object) [
                'primaryStat' => (object) ['value' => 2000],
                'quantity' => 1,
            ],
            [
                (object) ['isEnabled' => true, 'isVisible' => true, 'plugHash' => 2001],
                (object) ['isEnabled' => true, 'isVisible' => true, 'plugHash' => 2002],
                (object) ['isEnabled' => false, 'isVisible' => true, 'plugHash' => 2003],
            ],
            false,
            false,
            null,
            null,
            new FakeEquipmentManifest([
                'InventoryItem:1001' => (object) [
                    'displayProperties' => (object) ['name' => 'Test Helmet'],
                    'inventory' => (object) [
                        'bucketTypeHash' => 3448274439,
                        'tierTypeHash' => 4008398120,
                    ],
                    'itemTypeDisplayName' => 'Helmet',
                    'redacted' => false,
                ],
                'InventoryItem:2001' => (object) [
                    'investmentStats' => [
                        (object) ['statTypeHash' => 392767087, 'value' => 12],
                        (object) ['statTypeHash' => 1735777505, 'value' => 5],
                    ],
                ],
                'InventoryItem:2002' => (object) [
                    'investmentStats' => [
                        (object) ['statTypeHash' => 392767087, 'value' => 3],
                    ],
                ],
                'InventoryItem:2003' => (object) [
                    'investmentStats' => [
                        (object) ['statTypeHash' => 4244567218, 'value' => 99],
                    ],
                ],
            ]),
        );

        $this->assertSame([
            'Health' => 15,
            'Grenade' => 5,
        ], $item->stats);
    }

    public function test_it_only_includes_shader_and_ornament_when_requested(): void
    {
        $withoutCosmetics = new EquipmentItem((object) [
            'itemInstanceId' => 'instance-2',
            'itemHash' => 1002,
        ]);

        $withCosmetics = new EquipmentItem((object) [
            'itemInstanceId' => 'instance-3',
            'itemHash' => 1002,
        ]);

        $manifest = new FakeEquipmentManifest([
            'InventoryItem:1002' => (object) [
                'displayProperties' => (object) ['name' => 'Test Weapon'],
                'inventory' => (object) [
                    'bucketTypeHash' => 1498876634,
                    'tierTypeHash' => 4008398120,
                ],
                'itemTypeDisplayName' => 'Auto Rifle',
                'redacted' => false,
            ],
            'InventoryItem:2004' => (object) [
                'displayProperties' => (object) ['name' => 'Default Shader'],
                'inventory' => (object) ['bucketTypeHash' => 2422292810],
            ],
            'InventoryItem:2005' => (object) [
                'displayProperties' => (object) ['name' => 'Default Ornament'],
                'inventory' => (object) ['bucketTypeHash' => 2422292810],
            ],
            'InventoryItem:2006' => (object) [
                'displayProperties' => (object) ['name' => 'Targeting Mod'],
                'inventory' => (object) ['bucketTypeHash' => 3313201758],
            ],
        ]);

        $sockets = [
            (object) ['isEnabled' => true, 'isVisible' => true, 'plugHash' => 2004],
            (object) ['isEnabled' => true, 'isVisible' => true, 'plugHash' => 2005],
            (object) ['isEnabled' => true, 'isVisible' => true, 'plugHash' => 2006],
        ];

        $withoutCosmetics->load((object) ['primaryStat' => (object) ['value' => 2000], 'quantity' => 1], $sockets, true, false, null, null, $manifest);
        $withCosmetics->load((object) ['primaryStat' => (object) ['value' => 2000], 'quantity' => 1], $sockets, true, true, null, null, $manifest);

        $this->assertSame(['Targeting Mod'], $withoutCosmetics->perks);
        $this->assertSame(['Default Shader', 'Default Ornament', 'Targeting Mod'], $withCosmetics->perks);
    }
}

class FakeEquipmentManifest extends Manifest
{
    public function __construct(
        private array $definitions,
    ) {}

    public function getDefinition(string $strTableName, int|string $id): object|false
    {
        return $this->definitions[$strTableName.':'.$id] ?? false;
    }
}
