<?php

namespace Tests\Unit;

use App\Command\CommandContext;
use App\Command\Resolution\CommandPlayerResolver;
use PHPUnit\Framework\TestCase;

class CommandPlayerResolverTest extends TestCase
{
    public function test_it_can_resolve_a_requested_player_from_the_command_context(): void
    {
        $command = new CommandContext;
        $command->setQuery('kd');
        $command->setRequestedPlayer('12345', '4', 'Guardian#1234');

        $players = (new CommandPlayerResolver)->getPlayers($command);

        $this->assertArrayHasKey('Guardian#1234', $players);
        $this->assertSame('12345', $players['Guardian#1234']->membershipId);
        $this->assertSame('4', $players['Guardian#1234']->membershipType);
        $this->assertSame('Guardian#1234', $players['Guardian#1234']->displayName);
    }
}
