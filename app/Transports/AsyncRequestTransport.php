<?php

namespace App\Transports;

use Exception;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Promise\Utils;
use Illuminate\Support\Facades\Log;

class AsyncRequestTransport
{
    private array $queue = [];

    public function addRequest(object $request, string $category, string $identifier): void
    {
        $this->queue[$category][$identifier] = $request;
    }

    public function execute(string $category, int $attempt = 0): array
    {
        /*
        So this request function is a bit messy. Still not sure if its on my or Bungies end, but cURL is throwing ALOT of 'name lookup timed out' errors.
        Since we're working with a variety bots with different timeouts, we have to send a response in max 5-8 seconds.
        Temporary fix, have a low connect_timeout and just try to connect a few times. This reduced the timeout errors alot, however a few request will still fail after 5 attempts.
        */

        $bungieTransport = new BungieTransport;
        $client = $bungieTransport->guzzleClient();

        $pendingRequests = [];
        if (! empty($this->queue)) {
            foreach ($this->queue as $requestCategory => $categoryRequests) {
                if ($requestCategory === $category) {
                    foreach ($categoryRequests as $identifier => $request) {
                        $requestOptions = [];

                        if ($bungieTransport->isBungieUrl($request->url)) {
                            $requestOptions['headers'] = $bungieTransport->headers(includeOrigin: true);
                        }

                        if ($request->method == 'GET') {
                            $pendingRequests[$identifier] = $client->requestAsync($request->method, $request->url, $requestOptions);
                        } elseif ($request->method == 'POST') {
                            $requestOptions['json'] = $request->postFields;
                            $requestOptions['headers'] = array_merge($requestOptions['headers'] ?? [], [
                                'Content-Type' => 'application/json',
                            ]);
                            $pendingRequests[$identifier] = $client->requestAsync($request->method, $request->url, $requestOptions);
                        }
                    }
                }
            }
        }

        $responses = [];
        if (! empty($pendingRequests)) {
            foreach (Utils::settle($pendingRequests)->wait() as $identifier => $result) {
                if ($result['state'] === 'fulfilled') {
                    $response = $result['value'];
                    switch ($response->getStatusCode()) {
                        case 200:
                            $responses[$identifier] = json_decode($response->getBody()->getContents());
                            break;

                        default:
                            $responseBody = (string) $response->getBody();

                            Log::error('DC503 upstream request returned unexpected status', [
                                'code' => 'DC503',
                                'category' => $category,
                                'identifier' => $identifier,
                                'status' => $response->getStatusCode(),
                                'url' => $this->queue[$category][$identifier]->url ?? null,
                                'content_type' => $response->getHeaderLine('Content-Type'),
                                'response_body' => mb_substr($responseBody, 0, 2000),
                            ]);

                            throw new Exception('Something went wrong, please try again later (#DC503)');
                    }
                } else {
                    if ($result['reason'] instanceof ConnectException) {
                        if ($attempt <= 5) {
                            $attempt++;
                            $handlerContext = $result['reason']->getHandlerContext();
                            Log::debug($category.' - '.$attempt.' attempt - '.$result['reason']->getCode().' - '.$result['reason']->getMessage().(isset($handlerContext['url']) ? ' - '.$handlerContext['url'] : ''));

                            return $this->execute($category, $attempt);
                        }

                        Log::error('DC504 upstream request failed after retries', [
                            'code' => 'DC504',
                            'category' => $category,
                            'identifier' => $identifier,
                            'attempts' => $attempt,
                            'message' => $result['reason']->getMessage(),
                        ]);
                    } else {
                        Log::error('DC505 upstream request rejected', [
                            'code' => 'DC505',
                            'category' => $category,
                            'identifier' => $identifier,
                            'reason' => (string) $result['reason'],
                        ]);
                    }
                }
            }
        }

        return $responses;
    }
}
