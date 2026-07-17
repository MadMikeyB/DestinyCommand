<?php

namespace Tests\Unit;

use App\Command\Action;
use App\Command\Providers\BungieProvider;
use PHPUnit\Framework\TestCase;

class CommandActionTest extends TestCase
{
    public function test_it_builds_vendor_actions(): void
    {
        $action = new Action('xur');

        $this->assertSame('xur', $action->key);
        $this->assertSame(BungieProvider::class, $action->provider);
        $this->assertSame('vendor', $action->endpoint);
        $this->assertSame('getSales', $action->filter);
        $this->assertTrue($action->noUser);
        $this->assertSame(2190858386, $action->options->hash);
        $this->assertSame([400, 402], $action->options->params['components']);
    }

    public function test_it_builds_the_documented_primary_example_as_a_profile_equipment_action(): void
    {
        $action = new Action('primary');

        $this->assertSame('primary', $action->key);
        $this->assertSame(BungieProvider::class, $action->provider);
        $this->assertSame('profile', $action->endpoint);
        $this->assertSame('getCharacterEquipment', $action->filter);
        $this->assertTrue($action->options->perks);
        $this->assertTrue($action->options->latest);
        $this->assertSame(['primary'], $action->options->field);
    }

    public function test_it_resolves_action_aliases_before_building_stats_actions(): void
    {
        $action = new Action('mk');

        $this->assertSame('mk', $action->key);
        $this->assertSame(BungieProvider::class, $action->provider);
        $this->assertSame('stats', $action->endpoint);
        $this->assertSame('getStats', $action->filter);
        $this->assertSame(['bestSingleGameKills'], $action->options->field);
        $this->assertSame(5, $action->options->modes);
        $this->assertSame('General', $action->options->groups);
        $this->assertFalse($action->options->seperate);
        $this->assertFalse($action->options->pga);
    }

    public function test_it_falls_back_to_default_info_for_unknown_actions(): void
    {
        $action = new Action('doesnotexist');

        $this->assertSame('default_info', $action->key);
        $this->assertSame('plain_text', $action->provider);
        $this->assertTrue($action->noUser);
        $this->assertStringContainsString('Usage !destiny <action> <user> <platform>', $action->text);
    }

    public function test_it_builds_character_progression_actions_from_enum_backed_metadata(): void
    {
        $action = new Action('card');

        $this->assertSame('card', $action->key);
        $this->assertSame(BungieProvider::class, $action->provider);
        $this->assertSame('profile', $action->endpoint);
        $this->assertSame('getCharacterProgression', $action->filter);
        $this->assertTrue($action->options->latest);
        $this->assertSame([1062449239, 2093709363], $action->options->progressions);
    }

    public function test_it_builds_character_profile_actions_from_enum_backed_metadata(): void
    {
        $action = new Action('cpowerlevel');

        $this->assertSame('powerlevel', $action->key);
        $this->assertSame(BungieProvider::class, $action->provider);
        $this->assertSame('profile', $action->endpoint);
        $this->assertSame('getCharacterProfileValue', $action->filter);
        $this->assertSame('light', $action->options->field);
    }

    public function test_it_builds_playlist_stat_actions_from_enum_backed_metadata(): void
    {
        $action = new Action('pvekd');

        $this->assertSame('kd', $action->key);
        $this->assertSame(BungieProvider::class, $action->provider);
        $this->assertSame('stats', $action->endpoint);
        $this->assertSame(7, $action->options->modes);
        $this->assertSame(['killsDeathsRatio'], $action->options->field);
    }

    public function test_it_builds_the_documented_ckd_example_as_separate_character_stats(): void
    {
        $action = new Action('ckd');

        $this->assertSame('kd', $action->key);
        $this->assertSame(5, $action->options->modes);
        $this->assertTrue($action->options->seperate);
        $this->assertFalse($action->options->pga);
    }

    public function test_it_builds_the_documented_cpvekd_example_as_separate_pve_character_stats(): void
    {
        $action = new Action('cpvekd');

        $this->assertSame('kd', $action->key);
        $this->assertSame(7, $action->options->modes);
        $this->assertTrue($action->options->seperate);
        $this->assertFalse($action->options->pga);
    }

    public function test_it_builds_trials_report_actions_from_enum_backed_metadata(): void
    {
        $action = new Action('trialsteam');

        $this->assertSame('TrialsTeam', $action->key);
        $this->assertSame('getFireteam', $action->endpoint);
        $this->assertSame('getFireteamStats', $action->filter);
        $this->assertTrue($action->options->team);
    }

    public function test_it_builds_gambit_stat_actions_from_dedicated_metadata(): void
    {
        $action = new Action('invasions');

        $this->assertSame('invasions', $action->key);
        $this->assertSame(BungieProvider::class, $action->provider);
        $this->assertSame('stats', $action->endpoint);
        $this->assertSame(63, $action->options->modes);
        $this->assertSame(['invasions'], $action->options->field);
        $this->assertSame('General', $action->options->groups);
    }

    public function test_it_builds_gambit_medal_actions_from_dedicated_metadata(): void
    {
        $action = new Action('armyofone');

        $this->assertSame('armyofone', $action->key);
        $this->assertSame(BungieProvider::class, $action->provider);
        $this->assertSame('stats', $action->endpoint);
        $this->assertSame(63, $action->options->modes);
        $this->assertSame(['medals_pvecomp_medal_invader_kill_four'], $action->options->field);
        $this->assertSame('Medals', $action->options->groups);
    }
}
