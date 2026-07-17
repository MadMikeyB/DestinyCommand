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
        $response->assertSeeText('xgerhard: Rose [2000] [Rapid Hit] [Default Shader, Default Ornament].');
    }

    public function test_armor_commands_include_stats_and_set_bonuses(): void
    {
        $response = $this->get('/api/command?query=helmet%20xgerhard%231234%20xbox&user=Viewer');

        $response->assertOk();
        $response->assertSeeText('@Viewer:');
        $response->assertSeeText("xgerhard: Techeun's Regalia Helmet [2000] [Mobility 2, Resilience 20, Recovery 12, Discipline 9, Intellect 10, Strength 15] [Empty Mod Socket] [Default Shader, Default Ornament] [Set: Techeun's Regalia (2 equipped)].");
    }

    public function test_aggregate_gear_command_does_not_include_shader_or_ornament(): void
    {
        $response = $this->get('/api/command?query=gear%20xgerhard%231234%20xbox&user=Viewer');

        $response->assertOk();
        $response->assertSeeText('@Viewer:');
        $response->assertSeeText("xgerhard: Techeun's Regalia Helmet [2000] [Mobility 2, Resilience 20, Recovery 12, Discipline 9, Intellect 10, Strength 15] [Empty Mod Socket] [Set: Techeun's Regalia (2 equipped)], Techeun's Regalia Chest [1999] [Mobility 2, Resilience 20, Recovery 12, Discipline 9, Intellect 10, Strength 15] [Empty Mod Socket] [Set: Techeun's Regalia (2 equipped)].");
        $response->assertDontSeeText('Default Shader');
        $response->assertDontSeeText('Default Ornament');
    }

    public function test_artifact_command_returns_the_equipped_artifact(): void
    {
        $response = $this->get('/api/command?query=artifact%20xgerhard%231234%20xbox&user=Viewer');

        $response->assertOk();
        $response->assertSeeText('@Viewer:');
        $response->assertSeeText('xgerhard: Slayer Baron Apothecary Satchel [2000] [Piercing Sidearms].');
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
        $primaryResponse->assertSeeText('xgerhard: Rose [2000] [Rapid Hit] [Default Shader, Default Ornament].');
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
                    'character-1' => [new FakeEquipmentItem('Rose', 2000, $this->perksForAction($oAction, ['Rapid Hit']))],
                ],
            ];
        }

        if ($oAction->key === 'helmet') {
            return [
                $resultKey => [
                    'character-1' => [new FakeEquipmentItem(
                        "Techeun's Regalia Helmet",
                        2000,
                        $this->perksForAction($oAction, ['Empty Mod Socket']),
                        [
                            'Mobility' => 2,
                            'Resilience' => 20,
                            'Recovery' => 12,
                            'Discipline' => 9,
                            'Intellect' => 10,
                            'Strength' => 15,
                        ],
                        [
                            'name' => "Techeun's Regalia",
                            'equippedCount' => 2,
                            'bonuses' => [
                                ['required' => 2, 'name' => 'Queensfoil Rush'],
                                ['required' => 4, 'name' => 'Truth to Power'],
                            ],
                        ],
                    )],
                ],
            ];
        }

        if ($oAction->key === 'gear') {
            return [
                $resultKey => [
                    'character-1' => [
                        new FakeEquipmentItem(
                            "Techeun's Regalia Helmet",
                            2000,
                            $this->perksForAction($oAction, ['Empty Mod Socket']),
                            [
                                'Mobility' => 2,
                                'Resilience' => 20,
                                'Recovery' => 12,
                                'Discipline' => 9,
                                'Intellect' => 10,
                                'Strength' => 15,
                            ],
                            [
                                'name' => "Techeun's Regalia",
                                'equippedCount' => 2,
                                'bonuses' => [
                                    ['required' => 2, 'name' => 'Queensfoil Rush'],
                                    ['required' => 4, 'name' => 'Truth to Power'],
                                ],
                            ],
                        ),
                        new FakeEquipmentItem(
                            "Techeun's Regalia Chest",
                            1999,
                            $this->perksForAction($oAction, ['Empty Mod Socket']),
                            [
                                'Mobility' => 2,
                                'Resilience' => 20,
                                'Recovery' => 12,
                                'Discipline' => 9,
                                'Intellect' => 10,
                                'Strength' => 15,
                            ],
                            [
                                'name' => "Techeun's Regalia",
                                'equippedCount' => 2,
                                'bonuses' => [
                                    ['required' => 2, 'name' => 'Queensfoil Rush'],
                                    ['required' => 4, 'name' => 'Truth to Power'],
                                ],
                            ],
                        ),
                    ],
                ],
            ];
        }

        if ($oAction->key === 'artifact') {
            return [
                $resultKey => [
                    'character-1' => [new FakeEquipmentItem('Slayer Baron Apothecary Satchel', 2000, ['Piercing Sidearms'])],
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

    private function perksForAction(object $action, array $basePerks): array
    {
        if (($action->options->includeCosmetics ?? false) === false) {
            return $basePerks;
        }

        return array_merge($basePerks, ['Default Shader', 'Default Ornament']);
    }
}

class FakeEquipmentItem extends EquipmentItem
{
    public function __construct(
        public string $name,
        public int $light,
        public array $perks = [],
        public array $stats = [],
        public ?array $setBonuses = null,
    ) {}
}
