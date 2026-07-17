<?php

namespace App;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Promise\Utils;
use Illuminate\Support\Facades\Log;

class RequestHandler
{
    private $q = [];

    public function addRequest($oRequest, $strCategory, $strIdentifier)
    {
        $this->q[$strCategory][$strIdentifier] = $oRequest;
    }

    public function requester($xKey, $i = 0)
    {
        /*
        So this request function is a bit messy. Still not sure if its on my or Bungies end, but cURL is throwing ALOT of 'name lookup timed out' errors.
        Since we're working with a variety bots with different timeouts, we have to send a response in max 5-8 seconds.
        Temporary fix, have a low connect_timeout and just try to connect a few times. This reduced the timeout errors alot, however a few request will still fail after 5 attempts.
        */

        $oClient = new Client([
            'http_errors' => false,
            'verify' => false,
            'timeout' => 6, // Response timeout
            'connect_timeout' => 1.5,
            'headers' => [
                'X-API-Key' => config('destinycommand.bungie_api_key'),
                'Origin' => config('destinycommand.request_origin'),
                'User-Agent' => config('destinycommand.user_agent'),
            ],
            'force_ip_resolve' => 'v4',
        ]);

        $a = [];
        if (! empty($this->q)) {
            foreach ($this->q as $strCategory => $aCategoryValue) {
                if ($strCategory === $xKey) {
                    foreach ($aCategoryValue as $strIdentifier => $oRequest) {
                        if ($oRequest->method == 'GET') {
                            $a[$strIdentifier] = $oClient->requestAsync($oRequest->method, $oRequest->url);
                        } elseif ($oRequest->method == 'POST') {
                            $a[$strIdentifier] = $oClient->requestAsync($oRequest->method, $oRequest->url, ['json' => $oRequest->postFields, 'content-type' => 'application/json']);
                        }
                    }
                }
            }
        }

        $aReturn = [];
        if (! empty($a)) {
            foreach (Utils::settle($a)->wait() as $strKey => $aResult) {
                if ($aResult['state'] === 'fulfilled') {
                    $oResponse = $aResult['value'];
                    switch ($oResponse->getStatusCode()) {
                        case 200:
                            $aReturn[$strKey] = json_decode($oResponse->getBody()->getContents());
                            break;

                        default: // case 503:
                            $responseBody = (string) $oResponse->getBody();

                            Log::error('DC503 upstream request returned unexpected status', [
                                'code' => 'DC503',
                                'category' => $xKey,
                                'identifier' => $strKey,
                                'status' => $oResponse->getStatusCode(),
                                'url' => $this->q[$xKey][$strKey]->url ?? null,
                                'content_type' => $oResponse->getHeaderLine('Content-Type'),
                                'response_body' => mb_substr($responseBody, 0, 2000),
                            ]);

                            throw new Exception('Something went wrong, please try again later (#DC503)');
                            break;
                    }
                } else {
                    if ($aResult['reason'] instanceof ConnectException) {
                        if ($i <= 5) {
                            $i++;
                            $aHandlerContext = $aResult['reason']->getHandlerContext();
                            Log::debug($xKey.' - '.$i.' attempt - '.$aResult['reason']->getCode().' - '.$aResult['reason']->getMessage().(isset($aHandlerContext['url']) ? ' - '.$aHandlerContext['url'] : ''));

                            return $this->requester($xKey, $i);
                        }

                        Log::error('DC504 upstream request failed after retries', [
                            'code' => 'DC504',
                            'category' => $xKey,
                            'identifier' => $strKey,
                            'attempts' => $i,
                            'message' => $aResult['reason']->getMessage(),
                        ]);
                    } else {
                        Log::error('DC505 upstream request rejected', [
                            'code' => 'DC505',
                            'category' => $xKey,
                            'identifier' => $strKey,
                            'reason' => (string) $aResult['reason'],
                        ]);
                    }
                }
            }
        }

        return $aReturn;
    }
}
