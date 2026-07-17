<?php

namespace App\Http\Controllers;

use App\Command\CommandContext;
use App\Command\Execution\CommandExecutor;
use App\Command\Formatting\CommandResponseFormatter;
use App\Command\Parsing\CommandRequestFactory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CommandController extends Controller
{
    private ?CommandContext $command = null;

    public function __construct(
        private CommandExecutor $commandExecutor,
        private CommandRequestFactory $commandRequestFactory,
        private CommandResponseFormatter $commandResponseFormatter,
    ) {}

    public function parseRequest(Request $request)
    {
        try {
            $commandData = $this->commandRequestFactory->createFromRequest($request);
            $this->command = $commandData['command'];

            if (! $this->shouldCache($request, $this->command)) {
                return $this->buildResponse($commandData);
            }

            $payload = Cache::remember(
                $this->cacheKey($request),
                now()->addMinute(),
                fn (): array => $this->buildResponsePayload($commandData),
            );

            return $this->responseFromPayload($payload);
        } catch (Exception $e) {
            return '@'.($this->command->responseUser ?? 'System').': '.$e->getMessage().'.';
        }
    }

    /**
     * Build the response for a parsed command request.
     */
    private function buildResponse(array $commandData)
    {
        return $this->responseFromPayload($this->buildResponsePayload($commandData));
    }

    /**
     * Build a cacheable payload for a parsed command request.
     *
     * @return array{type: string, content: string}
     */
    private function buildResponsePayload(array $commandData): array
    {
        $result = $this->commandExecutor->execute($this->command);

        if ($this->command->platform === 'json') {
            return [
                'type' => 'json',
                'content' => response()->json($result)->getContent(),
            ];
        }

        return [
            'type' => 'text',
            'content' => $this->commandResponseFormatter->format(
                $this->command,
                $result,
                $result['prep'] ?? [],
                $commandData['display_username'],
                $commandData['display_gamertag'],
            ),
        ];
    }

    /**
     * Rehydrate a cached command payload into an HTTP response.
     */
    private function responseFromPayload(array $payload)
    {
        if ($payload['type'] === 'json') {
            return response($payload['content'], 200, [
                'Content-Type' => 'application/json',
            ]);
        }

        return $payload['content'];
    }

    /**
     * Determine whether a command request can be cached briefly.
     */
    private function shouldCache(Request $request, CommandContext $command): bool
    {
        if ($request->has('refresh_xur')) {
            return false;
        }

        foreach ($command->query->actions as $action) {
            if (in_array($action->key, ['setplayer', 'setaccount', 'setxur', 'ratemybutt'], true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Build a stable cache key for repeat command requests.
     */
    private function cacheKey(Request $request): string
    {
        return 'command-response:'.sha1(json_encode([
            'url' => $request->fullUrl(),
            'nightbot_user' => $request->header('Nightbot-User'),
            'nightbot_channel' => $request->header('Nightbot-Channel'),
        ]));
    }
}
