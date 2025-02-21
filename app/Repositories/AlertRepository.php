<?php

namespace App\Repositories;

use App\Services\ElasticsearchService;

class AlertRepository
{
    public function __construct(protected ?ElasticsearchService $elasticService = null) {}

    public function getUserAlerts(int $userId)
    {
        return $this->elasticService->search(config('index.alert.name'), ['user_id' => $userId])['data'] ?? [];
    }

    public function userHasAlert(int $userId, string $source)
    {
        $alerts = collect($this->getUserAlerts($userId));

        return $alerts->firstWhere('_source.source', $source) != null;
    }

    public function storeUserAlert(int $userId, string $source): bool
    {
        return $this->elasticService->indexDocument(config('index.alert.name'), [
            'user_id' => $userId,
            'source' => $source,
        ]);
    }
}
