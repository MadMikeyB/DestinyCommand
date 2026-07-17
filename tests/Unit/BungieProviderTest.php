<?php

namespace Tests\Unit;

use App\Command\Action;
use App\Command\Providers\BungieProvider;
use App\Destiny\DestinyClient;
use PHPUnit\Framework\TestCase;

class BungieProviderTest extends TestCase
{
    public function test_primary_prepare_flow_returns_null_and_queues_a_profile_request(): void
    {
        $client = new FakeDestinyClient;
        $provider = new BungieProvider($client);

        $result = $provider->fetch(new Action('primary'), [
            'players' => [(object) ['membershipType' => 1, 'membershipId' => 99]],
        ], true);

        $this->assertNull($result);
        $this->assertSame([[1, 99, [205, 304, 305, 300, 200]]], $client->profileCalls);
    }

    public function test_kd_prepare_flow_returns_null_and_queues_historical_stats(): void
    {
        $client = new FakeDestinyClient;
        $provider = new BungieProvider($client);

        $result = $provider->fetch(new Action('kd'), [
            'players' => [(object) ['membershipType' => 1, 'membershipId' => 99]],
        ], true);

        $this->assertNull($result);
        $this->assertSame([[1, 99, 0, ['modes' => 5, 'groups' => 'General']]], $client->historicalStatsCalls);
    }

    public function test_ckd_prepare_flow_returns_character_class_hashes_for_documented_character_stats(): void
    {
        $client = new FakeDestinyClient([
            'getProfile' => [
                '1-99' => (object) [
                    'characters' => (object) [
                        'data' => (object) [
                            'character-1' => (object) [
                                'characterId' => 'character-1',
                                'classHash' => 671679327,
                                'membershipType' => 1,
                                'membershipId' => 99,
                            ],
                        ],
                    ],
                ],
            ],
        ]);
        $provider = new BungieProvider($client);

        $result = $provider->fetch(new Action('ckd'), [
            'players' => [(object) ['membershipType' => 1, 'membershipId' => 99]],
        ], true);

        $this->assertSame(['character-1' => 671679327], $result);
        $this->assertSame([[1, 99, [200]]], $client->profileCalls);
        $this->assertSame([[1, 99, 'character-1', ['modes' => 5, 'groups' => 'General']]], $client->historicalStatsCalls);
    }
}

class FakeDestinyClient extends DestinyClient
{
    public array $profileCalls = [];

    public array $historicalStatsCalls = [];

    public function __construct(
        private array $responses = [],
    ) {}

    public function getProfile(int|string $iMembershipType, int|string $iMembershipId, array $aComponents = []): void
    {
        $this->profileCalls[] = [$iMembershipType, $iMembershipId, $aComponents];
    }

    public function getHistoricalStats(int|string $iMembershipType, int|string $iMembershipId, int|string $iCharacterId, array $aParams = []): void
    {
        $this->historicalStatsCalls[] = [$iMembershipType, $iMembershipId, $iCharacterId, $aParams];
    }

    public function get(string $strCategory): array
    {
        return $this->responses[$strCategory] ?? [];
    }
}
