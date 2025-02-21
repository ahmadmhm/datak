<?php

namespace App\Observers;

use App\Models\User;
use App\Repositories\AlertRepository;
use App\Services\ElasticsearchService;

class NewsObserver
{
    public function __construct(protected $userId, protected ?ElasticsearchService $elasticService = null) {}

    public function created(string $index)
    {
        $sendAlert = app(AlertRepository::class)->userHasAlert($this->userId, $index);
        if ($sendAlert) {
            User::find($this->userId)?->notify('You have a new item');
        }
    }
}
