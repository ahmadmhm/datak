<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\V1\AlertRequest;
use App\Repositories\AlertRepository;

class AlertController extends ApiController
{
    public function __construct(protected ?AlertRepository $alertRepository = null) {}

    public function store(AlertRequest $request)
    {
        $queryData = [
            'user_id' => config('index.alert.default_user'),
            'source' => $request->validated()['name'],
        ];
        $alerts = $this->alertRepository->getUserAlerts($queryData['user_id']);
        if (count($alerts) >= config('index.alert.max_count')) {
            return $this->generateErrorResponse([], 'You have reached to maximum alert count');
        }
        $oldAlert = $this->alertRepository->userHasAlert($queryData['user_id'], $queryData['source']);
        if ($oldAlert) {
            return $this->generateErrorResponse([], 'You have stored this resource before');
        }
        $newAlert = $this->alertRepository->storeUserAlert($queryData['user_id'], $queryData['source']);

        if ($newAlert) {
            return $this->generateResponse([], message: 'You have stored successfully');
        }

        return $this->generateErrorResponse([], 'There is exist a problem pls try later.');
    }
}
