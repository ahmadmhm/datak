<?php

namespace App\Services;

use App\Models\News;
use Illuminate\Database\Eloquent\Builder;

class SearchService
{
    public function generateQuery(): Builder
    {
        return News::query();
    }

    public function applyFilter(Builder $query, $request): void
    {
        if ($request->filled('title')) {
            $query->whereRaw("MATCH (title) AGAINST ('$request->title' IN BOOLEAN MODE)");
        }
        if ($request->filled('sources')) {
            $query->whereHas('source', fn ($q) => $q->whereIn('name', $request->sources));
        }
        if ($request->filled('authors')) {
            $authors = [];
            foreach ($request->authors as $author) {
                $authors = array_merge($authors, explode(' ', $author));
            }
            $authors = implode(' ', $request->authors);
            $query->whereRaw("MATCH (author) AGAINST ('$authors' IN BOOLEAN MODE)");
        }
        if ($request->filled('dateFrom')) {
            $query->where('published_at', '>=', $request->dateFrom);
        }
        if ($request->filled('dateTo')) {
            $query->where('published_at', '<=', $request->dateTo);
        }
        if ($request->filled('resource')) {
            $query->where('load_resource', $request->resource);
        }
    }
}
