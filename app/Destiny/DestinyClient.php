<?php

namespace App\Destiny;

use App\Transports\AsyncRequestTransport;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Queues and resolves Bungie API requests used by command execution.
 */
class DestinyClient
{
    private AsyncRequestTransport $r;

    private array $res = [];

    /**
     * Create a new Destiny client instance.
     */
    public function __construct()
    {
        $this->r = new AsyncRequestTransport;
    }

    /**
     * Queue a Bungie-name based player search request.
     */
    public function searchDestinyPlayerByBungieName(string $strDisplayName, int|string $iDisplayNameCode): void
    {
        $this->r->addRequest(
            new DestinyRequest('/Platform/Destiny2/SearchDestinyPlayerByBungieName/all/', [], 3400, 'POST', [
                'displayName' => $strDisplayName,
                'displayNameCode' => $iDisplayNameCode,
            ]),
            'searchDestinyPlayerByBungieName',
            $strDisplayName.'#'.$iDisplayNameCode
        );
    }

    /**
     * Queue a legacy Destiny player search request.
     */
    public function searchDestinyPlayer(string $strGamertag): void
    {
        $this->r->addRequest(
            new DestinyRequest('/Platform/Destiny2/SearchDestinyPlayer/-1/'.rawurlencode($strGamertag).'/', [], 3400),
            'searchDestinyPlayer',
            $strGamertag
        );
    }

    /**
     * Queue a Bungie user search request.
     */
    public function searchUsers(string $strUser): void
    {
        $this->r->addRequest(
            new DestinyRequest('/Platform/User/SearchUsers/', ['q' => $strUser], 0),
            'searchUsers',
            $strUser
        );
    }

    /**
     * Queue a profile request.
     */
    public function getProfile(int|string $iMembershipType, int|string $iMembershipId, array $aComponents = []): void
    {
        $this->r->addRequest(
            new DestinyRequest('/Platform/Destiny2/'.$iMembershipType.'/Profile/'.$iMembershipId.'/', ['components' => implode(',', $aComponents)], 0),
            'getProfile',
            $iMembershipType.'-'.$iMembershipId
        );
    }

    /**
     * Queue a linked profiles request.
     */
    public function getLinkedProfiles(int|string $iMembershipType, int|string $iMembershipId): void
    {
        $this->r->addRequest(
            new DestinyRequest('/Platform/Destiny2/'.$iMembershipType.'/Profile/'.$iMembershipId.'/LinkedProfiles/', [], 0),
            'getLinkedProfiles',
            $iMembershipType.'-'.$iMembershipId
        );
    }

    /**
     * Queue a Destiny manifest request.
     */
    public function getDestinyManifest(string|false $strDatabase = false): void
    {
        $this->r->addRequest(
            new DestinyRequest($strDatabase === false ? '/Platform/Destiny2/Manifest/' : $strDatabase),
            'getDestinyManifest',
            'getDestinyManifest'
        );
    }

    /**
     * Queue a historical stats request.
     */
    public function getHistoricalStats(int|string $iMembershipType, int|string $iMembershipId, int|string $iCharacterId, array $aParams = []): void
    {
        $this->r->addRequest(
            new DestinyRequest('/Platform/Destiny2/'.$iMembershipType.'/Account/'.$iMembershipId.'/Character/'.$iCharacterId.'/Stats/', $aParams),
            'getHistoricalStats',
            $iMembershipType.'-'.$iMembershipId.'-'.$iCharacterId
        );
    }

    /**
     * Queue a public vendors request.
     */
    public function getPublicVendors(array $aComponents = []): void
    {
        $this->r->addRequest(
            new DestinyRequest('/Platform/Destiny2/Vendors/', ['components' => implode(',', $aComponents)], 0),
            'getPublicVendors',
            'getPublicVendors'
        );
    }

    /**
     * Execute and return responses for a queued request category.
     */
    public function get(string $strCategory): array
    {
        if (! isset($this->res[$strCategory])) {
            $aResponses = $this->r->execute($strCategory);
            foreach ($aResponses as $strKey => $oResponse) {
                if (isset($oResponse->ErrorCode) && $oResponse->ErrorCode !== 1) {
                    throw new Exception($oResponse->Message);
                } else {
                    $this->res[$strCategory][$strKey] = $oResponse->Response;
                }
            }
        }
        if (! isset($this->res[$strCategory])) {
            Log::error('DC502 destiny client returned no usable response', [
                'code' => 'DC502',
                'category' => $strCategory,
            ]);

            throw new Exception('Something went wrong, please try again later (#DC502)');
        }

        return $this->res[$strCategory];
    }
}
