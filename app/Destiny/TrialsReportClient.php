<?php

namespace App\Destiny;

use App\RequestHandler;
use Exception;
use Illuminate\Support\Facades\Log;

class TrialsReportClient
{
    private $r;

    private $res = [];

    private $base_url = '';

    public function __construct()
    {
        $this->r = new RequestHandler;
    }

    public function getFireteam($iMembershipId, $iMembershipType)
    {
        $this->r->addRequest(
            new TrialsReportRequest('/player/'.$iMembershipId.'/fireteam'),
            'getFireteam',
            $iMembershipType.'-'.$iMembershipId
        );
    }

    public function get($strCategory)
    {
        if (! isset($this->res[$strCategory])) {
            $aResponses = $this->r->requester($strCategory);
            foreach ($aResponses as $strKey => $oResponse) {
                $this->res[$strCategory][$strKey] = $oResponse;
            }
        }
        if (! isset($this->res[$strCategory])) {
            Log::error('DC512 trials report client returned no usable response', [
                'code' => 'DC512',
                'category' => $strCategory,
            ]);

            throw new Exception('Something went wrong, please try again later (#DC512)');
        }

        return $this->res[$strCategory];
    }
}
