<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\V1\SearchArticleRequest;
use App\Models\News;
use App\Services\SearchService;

class SearchController extends ApiController
{
    public function __construct(protected $articleService = null)
    {
        $this->articleService = app(SearchService::class);
    }

    public function index(SearchArticleRequest $request)
    {
        News::factory(200)->create();
        $n = News::search('title:(Ms. OR Mr.)')->raw();
        dd($n);
        $articles = $this->articleService->generateQuery();
        $this->articleService->applyFilter($articles, $request);
        $articles = $articles->paginate();

        return $this->generateResponse($articles, true);
    }
}
