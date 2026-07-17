<?php

namespace Tests\Unit;

use App\Command\CommandContext;
use App\Command\Formatting\CommandResponseFormatter;
use App\Command\Query;
use App\Destiny\EquipmentItem;
use PHPUnit\Framework\TestCase;

class CommandResponseFormatterTest extends TestCase
{
    public function test_it_keeps_empty_mod_sockets_when_the_response_is_within_the_character_limit(): void
    {
        $formatter = new CommandResponseFormatter;
        $command = $this->makeCommandContext('Viewer');

        $response = $formatter->format($command, [
            'players' => [(object) [
                'membershipType' => 3,
                'membershipId' => 99,
                'displayName' => 'Guardian#1234',
            ]],
            'response' => [
                '3-99' => [
                    'character-1' => [
                        new FakeFormatterEquipmentItem('Helmet of Testing', 2000, ['Empty Mod Socket', 'Targeting Mod']),
                    ],
                ],
            ],
            'prep' => [],
        ]);

        $this->assertStringContainsString('[Empty Mod Socket, Targeting Mod]', $response);
    }

    public function test_it_renders_cosmetics_in_a_separate_bracket_group(): void
    {
        $formatter = new CommandResponseFormatter;
        $command = $this->makeCommandContext('Viewer');

        $response = $formatter->format($command, [
            'players' => [(object) [
                'membershipType' => 3,
                'membershipId' => 99,
                'displayName' => 'Guardian#1234',
            ]],
            'response' => [
                '3-99' => [
                    'character-1' => [
                        new FakeFormatterEquipmentItem('Helmet of Testing', 2000, ['Targeting Mod', 'Default Shader', 'Default Ornament']),
                    ],
                ],
            ],
            'prep' => [],
        ]);

        $this->assertStringContainsString('[Targeting Mod] [Default Shader, Default Ornament]', $response);
    }

    public function test_it_collapses_empty_mod_sockets_when_the_response_exceeds_the_character_limit(): void
    {
        $formatter = new CommandResponseFormatter;
        $command = $this->makeCommandContext('Viewer', 'gear Guardian xbox');

        $response = $formatter->format($command, [
            'players' => [(object) [
                'membershipType' => 3,
                'membershipId' => 99,
                'displayName' => 'Guardian#1234',
            ]],
            'response' => [
                '3-99' => [
                    'character-1' => [
                        new FakeFormatterEquipmentItem(
                            str_repeat('Very Long Armor Name ', 12),
                            2000,
                            array_merge(array_fill(0, 8, 'Empty Mod Socket'), ['Targeting Mod']),
                            ['Weapons' => 16, 'Health' => 23, 'Class' => 29],
                        ),
                    ],
                ],
            ],
            'prep' => [],
        ]);

        $this->assertStringContainsString('[W 16, H 23, C 29]', $response);
        $this->assertStringContainsString('[8x Empty Mod Socket, Targeting Mod]', $response);
        $this->assertLessThanOrEqual(400, mb_strlen($response));
    }

    public function test_it_removes_empty_mod_sockets_when_the_collapsed_response_still_exceeds_the_character_limit(): void
    {
        $formatter = new CommandResponseFormatter;
        $command = $this->makeCommandContext('Viewer', 'gear Guardian xbox');

        $response = $formatter->format($command, [
            'players' => [(object) [
                'membershipType' => 3,
                'membershipId' => 99,
                'displayName' => 'Guardian#1234',
            ]],
            'response' => [
                '3-99' => [
                    'character-1' => [
                        new FakeFormatterEquipmentItem(
                            str_repeat('Extremely Long Armor Name ', 18),
                            2000,
                            array_merge(array_fill(0, 20, 'Empty Mod Socket'), ['Targeting Mod']),
                            ['Weapons' => 16, 'Health' => 23, 'Class' => 29],
                        ),
                    ],
                ],
            ],
            'prep' => [],
        ]);

        $this->assertStringContainsString('[W 16, H 23, C 29]', $response);
        $this->assertStringContainsString('[Targeting Mod]', $response);
        $this->assertStringNotContainsString('x Empty Mod Socket', $response);
        $this->assertStringNotContainsString('Empty Mod Socket,', $response);
    }

    public function test_it_does_not_shorten_stat_labels_for_non_gear_overflow_responses(): void
    {
        $formatter = new CommandResponseFormatter;
        $command = $this->makeCommandContext('Viewer', 'helmet Guardian xbox');

        $response = $formatter->format($command, [
            'players' => [(object) [
                'membershipType' => 3,
                'membershipId' => 99,
                'displayName' => 'Guardian#1234',
            ]],
            'response' => [
                '3-99' => [
                    'character-1' => [
                        new FakeFormatterEquipmentItem(
                            str_repeat('Very Long Helmet Name ', 12),
                            2000,
                            array_merge(array_fill(0, 8, 'Empty Mod Socket'), ['Targeting Mod']),
                            ['Weapons' => 16, 'Health' => 23, 'Class' => 29],
                        ),
                    ],
                ],
            ],
            'prep' => [],
        ]);

        $this->assertStringContainsString('[Weapons 16, Health 23, Class 29]', $response);
        $this->assertStringNotContainsString('[W 16, H 23, C 29]', $response);
    }

    private function makeCommandContext(string $user, ?string $query = null): CommandContext
    {
        $command = new CommandContext;
        $command->setUser($user);
        $command->setPlatform('twitch');
        $command->query = $query !== null ? new Query($query) : null;

        return $command;
    }
}

class FakeFormatterEquipmentItem extends EquipmentItem
{
    public function __construct(
        public string $name,
        public int $light,
        public array $perks = [],
        public array $stats = [],
    ) {}
}
