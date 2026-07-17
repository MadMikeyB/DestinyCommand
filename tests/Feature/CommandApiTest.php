<?php

namespace Tests\Feature;

use App\Command\Execution\CommandExecutor;
use Mockery;
use Tests\TestCase;

class CommandApiTest extends TestCase
{
    public function test_command_api_returns_default_info_when_query_is_missing(): void
    {
        $response = $this->get('/api/command');

        $response->assertOk();
        $response->assertSee('Usage !destiny <action> <user> <platform>', false);
        $response->assertSeeText('Command list: destinycommand.com');
    }

    public function test_command_api_returns_help_text(): void
    {
        $response = $this->get('/api/command?query=help');

        $response->assertOk();
        $response->assertSeeText('@System:');
        $response->assertSee('Usage !destiny <action> <user> <platform>', false);
    }

    public function test_command_api_can_omit_the_username_prefix(): void
    {
        $response = $this->get('/api/command?query=commands&nousername=1');

        $response->assertOk();
        $response->assertDontSeeText('@System:');
        $response->assertSeeText('Command list: destinycommand.com');
    }

    public function test_command_api_supports_json_platform_output(): void
    {
        $response = $this->get('/api/command?query=commands&platform=json');

        $response->assertOk();
        $response->assertHeader('content-type', 'application/json');
        $response->assertJsonPath('players', []);
        $response->assertJsonPath('response.text.0', 'Command list: destinycommand.com');
    }

    public function test_command_api_reports_unauthorised_setxur_attempts(): void
    {
        $response = $this->get('/api/command?query=setxur%20tower');

        $response->assertOk();
        $response->assertSeeText('Not allowed to set Xur location');
    }

    public function test_command_api_caches_safe_repeated_requests_for_one_minute(): void
    {
        $executor = Mockery::mock(CommandExecutor::class);
        $executor->shouldReceive('execute')->once()->andReturn([
            'players' => [],
            'response' => ['text' => ['cached response']],
            'prep' => [],
        ]);

        $this->app->instance(CommandExecutor::class, $executor);

        $firstResponse = $this->get('/api/command?query=commands');
        $secondResponse = $this->get('/api/command?query=commands');

        $firstResponse->assertOk();
        $secondResponse->assertOk();
        $firstResponse->assertSeeText('cached response');
        $secondResponse->assertSeeText('cached response');
    }

    public function test_command_api_does_not_cache_mutating_requests(): void
    {
        $executor = Mockery::mock(CommandExecutor::class);
        $executor->shouldReceive('execute')->twice()->andReturn([
            'players' => [],
            'response' => ['text' => ['uncached response']],
            'prep' => [],
        ]);

        $this->app->instance(CommandExecutor::class, $executor);

        $firstResponse = $this->get('/api/command?query=setxur%20tower');
        $secondResponse = $this->get('/api/command?query=setxur%20tower');

        $firstResponse->assertOk();
        $secondResponse->assertOk();
        $firstResponse->assertSeeText('uncached response');
        $secondResponse->assertSeeText('uncached response');
    }
}
