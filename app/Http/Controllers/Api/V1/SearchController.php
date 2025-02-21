<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\V1\SearchRequest;
use App\Http\Resources\V1\NewsResource;
use App\Services\ElasticsearchService;

class SearchController extends ApiController
{
    public function __construct(protected ?ElasticsearchService $elasticService = null) {}

    public function index(SearchRequest $request)
    {
        $news = $this->elasticService->search('news,instagram,twitter', $request->validated());
        if ($news['success']) {
            return $this->generateResponse(NewsResource::collection($news['data']), true);
        }

        return $this->generateErrorResponse([], 'No data are found');
    }
}
