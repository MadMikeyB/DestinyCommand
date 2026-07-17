<?php

namespace Tests\Feature;

use App\Command\Providers\BungieProvider;
use App\Destiny\EquipmentItem;
use App\Destiny\Stat;
use App\Models\Destiny\DestinyBungiePlayer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommandApiDocumentedExamplesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        DestinyBungiePlayer::create([
            'membership_id' => 99,
            'membership_type' => 1,
            'display_name' => 'xgerhard',
            'display_code' => '1234',
        ]);

        $this->app->instance(BungieProvider::class, new FakeFeatureBungieProvider);
    }

    public function test_documented_primary_example_executes_end_to_end(): void
    {
        $response = $this->get('/api/command?query=primary%20xgerhard%231234%20xbox&user=Viewer');

        $response->assertOk();
        $response->assertSeeText('@Viewer:');
        $response->assertSeeText('xgerhard: Rose [2000] [Rapid Hit].');
    }

    public function test_documented_kd_example_executes_end_to_end(): void
    {
        $response = $this->get('/api/command?query=kd%20xgerhard%231234%20xbox&user=Viewer');

        $response->assertOk();
        $response->assertSeeText('xgerhard: [PvP] K/D: 1.50.');
    }

    public function test_documented_ckd_example_executes_end_to_end(): void
    {
        $response = $this->get('/api/command?query=ckd%20xgerhard%231234%20xbox&user=Viewer');

        $response->assertOk();
        $response->assertSeeText('xgerhard: [PvP] Hunter: K/D: 1.50.');
    }

    public function test_documented_pvekd_example_executes_end_to_end(): void
    {
        $response = $this->get('/api/command?query=pvekd%20xgerhard%231234%20xbox&user=Viewer');

        $response->assertOk();
        $response->assertSeeText('xgerhard: [PvE] K/D: 2.50.');
    }

    public function test_documented_cpvekd_example_executes_end_to_end(): void
    {
        $response = $this->get('/api/command?query=cpvekd%20xgerhard%231234%20xbox&user=Viewer');

        $response->assertOk();
        $response->assertSeeText('xgerhard: [PvE] Hunter: K/D: 2.50.');
    }

    public function test_documented_setplayer_and_follow_up_primary_example_execute_end_to_end(): void
    {
        $headers = [
            'Nightbot-User' => 'provider=twitch&providerId=123&displayName=Viewer',
        ];

        $setPlayerResponse = $this->withHeaders($headers)
            ->get('/api/command?query=setplayer%20xgerhard%231234%20xbox');

        $setPlayerResponse->assertOk();
        $setPlayerResponse->assertSeeText('Succesfully saved player: xgerhard');
        $this->assertDatabaseHas('user_players', [
            'provider' => 'twitch',
            'providerId' => '123',
            'destinyPlayerId' => DestinyBungiePlayer::firstOrFail()->id,
        ]);

        $primaryResponse = $this->withHeaders($headers)->get('/api/command?query=primary');

        $primaryResponse->assertOk();
        $primaryResponse->assertSeeText('@Viewer:');
        $primaryResponse->assertSeeText('xgerhard: Rose [2000] [Rapid Hit].');
    }

    public function test_documented_bot_query_string_shapes_execute_end_to_end(): void
    {
        $queries = [
            '/api/command?query=commands&bot=deepbot&user=Viewer&channel=Streamer&default_console=xbox',
            '/api/command?query=commands&bot=streamlabs&user=Viewer&channel=Streamer&default_console=xbox',
            '/api/command?query=commands&bot=phantombot&user=Viewer&channel=Streamer&default_console=xbox',
            '/api/command?query=commands&bot=streamelements&user=Viewer&channel=Streamer&default_console=xbox',
            '/api/command?query=commands&bot=mixitup&user=Viewer&channel=Streamer&default_console=xbox',
        ];

        foreach ($queries as $query) {
            $response = $this->get($query);

            $response->assertOk();
            $response->assertSeeText('@Viewer:');
            $response->assertSeeText('Command list: destinycommand.com');
        }
    }
}

class FakeFeatureBungieProvider extends BungieProvider
{
    public function __construct() {}

    public function fetch(object $oAction, array $aParameters, bool $bPrepare = false): mixed
    {
        if ($bPrepare) {
            return $oAction->options->seperate ?? false
                ? ['character-1' => 671679327]
                : null;
        }

        $player = reset($aParameters['players']);
        $resultKey = $player->membershipType.'-'.$player->membershipId;

        if ($oAction->key === 'primary') {
            return [
                $resultKey => [
                    'character-1' => [new FakeEquipmentItem('Rose', 2000, ['Rapid Hit'])],
                ],
            ];
        }

        $playlist = ($oAction->options->modes ?? 5) === 7 ? 'allPvE' : 'allPvP';
        $value = ($oAction->options->modes ?? 5) === 7 ? 2.5 : 1.5;
        $displayValue = ($oAction->options->modes ?? 5) === 7 ? '2.50' : '1.50';
        $characterKey = ($oAction->options->seperate ?? false) ? 'character-1' : 0;

        return [
            $resultKey => [
                $characterKey => [new Stat((object) [
                    'statId' => 'killsDeathsRatio',
                    'basic' => (object) ['value' => $value, 'displayValue' => $displayValue],
                ], $playlist)],
            ],
        ];
    }
}

class FakeEquipmentItem extends EquipmentItem
{
    public function __construct(
        public string $name,
        public int $light,
        public array $perks = [],
    ) {}
}
