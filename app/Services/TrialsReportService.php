<?php

namespace App\Services;

use App\Destiny\TrialsReportRequest;
use App\Transports\TrialsReportTransport;
use Exception;
use Illuminate\Support\Facades\Log;

class TrialsReportService
{
    private array $responses = [];

    public function __construct(
        private ?TrialsReportTransport $trialsReportTransport = null,
    ) {
        $this->trialsReportTransport ??= new TrialsReportTransport;
    }

    public function queueFireteam(int|string $membershipId, int|string $membershipType): void
    {
        $this->trialsReportTransport->queueRequest(
            new TrialsReportRequest('/player/'.$membershipId.'/fireteam'),
            'getFireteam',
            $membershipType.'-'.$membershipId,
        );
    }

    public function responsesFor(string $category): array
    {
        if (! isset($this->responses[$category])) {
            $this->responses[$category] = $this->trialsReportTransport->execute($category);
        }

        if ($this->responses[$category] === []) {
            Log::error('DC512 trials report service returned no usable response', [
                'code' => 'DC512',
                'category' => $category,
            ]);

            throw new Exception('Something went wrong, please try again later (#DC512)');
        }

        return $this->responses[$category];
    }
}
