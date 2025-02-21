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

        return $alerts->where('_source.source', $source)->where('_source.user_id', $userId)->first() != null;
    }

    public function storeUserAlert(int $userId, string $source): bool
    {
        dd($userId, $source);

        return $this->elasticService->indexDocument(config('index.alert.name'), [
            'user_id' => $userId,
            'source' => $source,
        ]);
    }
}
