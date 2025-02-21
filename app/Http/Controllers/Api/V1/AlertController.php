<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\V1\AlertRequest;
use App\Services\ElasticsearchService;

class AlertController extends ApiController
{
    public function __construct(protected ?ElasticsearchService $elasticService = null) {}

    public function store(AlertRequest $request)
    {
        $queryData = [
            'user_id' => 1,
            'source' => $request->validated()['name'],
        ];
        $alerts = $this->elasticService->search(config('index.alert.name'), ['user_id' => 1]);
        if ($alerts['success']) {
            if (count($alerts['data']) >= config('index.alert.max_count')) {
                return $this->generateErrorResponse([], 'You have reached to maximum alert count');
            }
            $oldAlert = $this->elasticService->search(config('index.alert.name'), $queryData);
            if (count($oldAlert['data']) > 0) {
                return $this->generateErrorResponse([], 'You have stored this resource before');
            }
            $newAlert = $this->elasticService->indexDocument(config('index.alert.name'), $queryData);

            if ($newAlert) {
                return $this->generateResponse([], message: 'You have stored successfully');
            }
        }

        return $this->generateErrorResponse([], 'There is exist a problem pls try later.');
    }
}
