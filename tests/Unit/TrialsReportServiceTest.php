<?php

namespace Tests\Unit;

use App\Destiny\TrialsReportRequest;
use App\Services\TrialsReportService;
use App\Transports\TrialsReportTransport;
use Tests\TestCase;

class TrialsReportServiceTest extends TestCase
{
    public function test_it_queues_fireteam_requests_through_the_transport(): void
    {
        $transport = new FakeTrialsReportTransport;
        $service = new TrialsReportService($transport);

        $service->queueFireteam(12345, 4);

        $this->assertCount(1, $transport->queuedRequests);
        $this->assertSame('getFireteam', $transport->queuedRequests[0]['category']);
        $this->assertSame('4-12345', $transport->queuedRequests[0]['identifier']);
        $this->assertInstanceOf(TrialsReportRequest::class, $transport->queuedRequests[0]['request']);
        $this->assertSame('https://api.trialsofthenine.com/player/12345/fireteam', $transport->queuedRequests[0]['request']->url);
    }

    public function test_it_caches_category_responses_after_the_first_fetch(): void
    {
        $transport = new FakeTrialsReportTransport([
            'getFireteam' => [
                '4-12345' => (object) ['results' => [(object) ['displayName' => 'Guardian']]],
            ],
        ]);
        $service = new TrialsReportService($transport);

        $firstResponse = $service->responsesFor('getFireteam');
        $secondResponse = $service->responsesFor('getFireteam');

        $this->assertSame($firstResponse, $secondResponse);
        $this->assertSame(1, $transport->executeCalls);
    }

    public function test_it_throws_when_the_transport_returns_no_usable_response(): void
    {
        $service = new TrialsReportService(new FakeTrialsReportTransport);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Something went wrong, please try again later (#DC512)');

        $service->responsesFor('getFireteam');
    }
}

class FakeTrialsReportTransport extends TrialsReportTransport
{
    public array $queuedRequests = [];

    public int $executeCalls = 0;

    public function __construct(
        private array $responses = [],
    ) {}

    public function queueRequest(TrialsReportRequest $request, string $category, string $identifier): void
    {
        $this->queuedRequests[] = [
            'request' => $request,
            'category' => $category,
            'identifier' => $identifier,
        ];
    }

    public function execute(string $category): array
    {
        $this->executeCalls++;

        return $this->responses[$category] ?? [];
    }
}
