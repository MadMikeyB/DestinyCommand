<?php

namespace Tests\Unit;

use App\Command\Query;
use PHPUnit\Framework\TestCase;

class CommandQueryTest extends TestCase
{
    public function test_it_parses_a_text_only_query_without_requiring_a_user(): void
    {
        $query = new Query('commands');

        $this->assertFalse($query->reqUser);
        $this->assertSame([], $query->gamertags);
        $this->assertSame([], $query->consoles);
        $this->assertArrayHasKey('commands', $query->actions);
        $this->assertSame('plain_text', $query->actions['commands']->provider);
    }

    public function test_it_parses_actions_gamertags_and_default_console(): void
    {
        $query = new Query('kd;weapons Guardian Name xbox');

        $this->assertTrue($query->reqUser);
        $this->assertSame(['Guardian Name'], $query->gamertags);
        $this->assertSame([1], $query->consoles);
        $this->assertSame(['kd', 'weapons'], array_keys($query->actions));
        $this->assertSame(['killsDeathsRatio'], $query->actions['kd']->options->field);
        $this->assertSame(['primary', 'secondary', 'heavy'], $query->actions['weapons']->options->field);
    }

    public function test_it_parses_artifact_as_a_supported_gear_action(): void
    {
        $query = new Query('artifact Guardian Name xbox');

        $this->assertTrue($query->reqUser);
        $this->assertArrayHasKey('artifact', $query->actions);
        $this->assertSame(['artifact'], $query->actions['artifact']->options->field);
    }

    public function test_it_supports_per_gamertag_console_overrides(): void
    {
        $query = new Query('kd Guardian One:ps;Guardian Two:steam xbox');

        $this->assertSame(['Guardian One', 'Guardian Two'], $query->gamertags);
        $this->assertSame([2, 3], $query->consoles);
    }

    public function test_it_normalizes_discord_double_dash_names(): void
    {
        $query = new Query('kd Test—User:steam');

        $this->assertSame(['Test--User'], $query->gamertags);
        $this->assertSame([3], $query->consoles);
    }
}
