<?php

namespace App\Transports;

use App\Destiny\TrialsReportRequest;

class TrialsReportTransport
{
    public function __construct(
        private ?AsyncRequestTransport $asyncRequestTransport = null,
    ) {
        $this->asyncRequestTransport ??= new AsyncRequestTransport;
    }

    public function queueRequest(TrialsReportRequest $request, string $category, string $identifier): void
    {
        $this->asyncRequestTransport->addRequest($request, $category, $identifier);
    }

    public function execute(string $category): array
    {
        return $this->asyncRequestTransport->execute($category);
    }
}
