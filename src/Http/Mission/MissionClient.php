<?php

declare(strict_types=1);

namespace App\Http\Mission;

use App\Http\Mission\Dto\MissionDto;
use Symfony\Component\HttpClient\CachingHttpClient;
use Symfony\Component\HttpClient\ScopingHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MissionClient
{
    /** @var HttpClientInterface */
    protected $client;

    public function __construct(HttpClientInterface $client, MissionStore $store, string $missionApiUrl)
    {
        $cachingClient = new CachingHttpClient($client, $store, [
            // allow 12h of stale response
            'stale_if_error' => 43200,
        ]);

        $this->client = ScopingHttpClient::forBaseUri($cachingClient, $missionApiUrl);
    }

    /**
     * @return \Generator|MissionDto[]
     */
    public function getMissions(bool $includeArchive = true, int $ttl = 600): \Generator
    {
        $response = $this->client->request('GET', '/api/missions', [
            'query' => [
                'includeArchive' => $includeArchive ? 'true' : 'false',
                'ttl' => $ttl,
            ],
        ]);

        foreach ($response->toArray() as $mission) {
            yield MissionDto::fromArray($mission);
        }
    }

    public function getNearestMission(): ?MissionDto
    {
        $upcomingMissions = iterator_to_array($this->getMissions(false));

        // api sorts missions latest to oldest
        return $upcomingMissions[count($upcomingMissions) - 1] ?? null;
    }
}
