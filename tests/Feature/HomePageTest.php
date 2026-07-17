<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomePageTest extends TestCase
{
    public function test_home_page_returns_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSeeText('DestinyCommand');
    }

    public function test_home_page_documents_command_usage_examples(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSeeText('!destiny <action> <user> <platform>');
        $response->assertSeeText('!destiny primary xgerhard#1234 xbox');
        $response->assertSeeText('!destiny kd xgerhard#1234');
        $response->assertSeeText('Will show account overall kd.');
        $response->assertSeeText('!destiny ckd xgerhard#1234');
        $response->assertSeeText('Will show kd per character.');
        $response->assertSeeText('!destiny pvekd xgerhard#1234');
        $response->assertSeeText('Will show account overall kd in PvE.');
        $response->assertSeeText('!destiny cpvekd xgerhard#1234');
        $response->assertSeeText('Will show kd per character in PvE.');
        $response->assertSeeText('!destiny setplayer username#1234 platform');
        $response->assertSeeText('!destiny primary');
    }

    public function test_home_page_documents_bot_install_snippets_and_steps(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSeeText('!commands add !destiny $(urlfetch https://destinycommand.com/api/command?query=$(querystring)&default_console=xbox)');
        $response->assertSeeText('!addcmd !destiny @customapi@[https://destinycommand.com/api/command?query=@target@&bot=deepbot&user=@user@&default_console=xbox&channel=CHANNEL_NAME_HERE]');
        $response->assertSeeText('Required: Change CHANNEL_NAME_HERE to your actual channel name.');
        $response->assertSeeText('!addcommand !destiny {readapi.https://destinycommand.com/api/command?query={1:3}&bot=streamlabs&user={user.name}&channel={channel.name}&default_console=xbox}');
        $response->assertSeeText('!addcom !destiny (customapi https://destinycommand.com/live/api/command?user=(sender)&channel=(channelname)&bot=phantombot&default_console=xbox&query=(encodeurlparam (echo)))');
        $response->assertSeeText('!command add !destiny ${customapi.https://destinycommand.com/api/command?query=$(queryencode $(1:))&bot=streamelements&user=$(queryencode ${user})&channel=$(queryencode ${channel})&default_console=xbox}');
        $response->assertSeeTextInOrder([
            'New command',
            'Download from Store',
            'Search "Destiny Stats"',
            'Select "Destiny Stats"',
            'Click download',
            'Insert a command name & chat trigger',
            'Click Save',
        ]);
        $response->assertSeeText('https://destinycommand.com/live/api/command?query=$allargs&bot=mixitup&user=$username&channel=$streamerusername&default_console=xbox');
        $response->assertSeeText('$webrequestresult');
        $response->assertSeeText('warmind.io');
        $response->assertSeeText('add Charlemagne to your server');
        $response->assertSeeText('Select your own, or a server you manage and hit Authorize.');
        $response->assertSeeText('default_console');
        $response->assertSeeText('pc, xbox or ps');
    }
}
