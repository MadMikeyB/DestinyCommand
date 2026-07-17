<?php

namespace Tests\Feature;

use Tests\TestCase;

class BungieNameConverterTest extends TestCase
{
    public function test_converter_page_is_accessible(): void
    {
        $response = $this->get('/tools/bungienameconverter');

        $response->assertOk();
        $response->assertSeeText('BungieName Converter');
        $response->assertSeeText('username#1234');
    }

    public function test_converter_shows_validation_errors_for_invalid_input(): void
    {
        $response = $this->post('/tools/bungienameconverter', [
            'username' => 'invalid-name',
            'url' => 'notaurl',
        ]);

        $response->assertOk();
        $response->assertSeeText('Url is not a valid url');
        $response->assertSeeText('Not a valid Bungie name');
    }

    public function test_converter_rewrites_legacy_command_urls_to_bungie_names(): void
    {
        $response = $this->post('/tools/bungienameconverter', [
            'username' => 'Guardian#1234',
            'url' => 'https://destinycommand.com/live/api/command?query=primary%20OldName&default_console=xbox&bot=nightbot',
        ]);

        $response->assertOk();
        $response->assertSeeText('New url:');
        $response->assertSee('<pre>https://destinycommand.com/live/api/command?query=primary%20Guardian%231234&amp;bot=nightbot</pre>', false);
    }
}
