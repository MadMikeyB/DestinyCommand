<?php

namespace Tests\Unit;

use App\Command\CommandContext;
use PHPUnit\Framework\TestCase;

class CommandContextTest extends TestCase
{
    public function test_it_defaults_bot_to_nightbot_when_null_is_provided(): void
    {
        $command = new CommandContext;

        $command->setBot(null);

        $this->assertSame('nightbot', $command->bot);
    }
}
