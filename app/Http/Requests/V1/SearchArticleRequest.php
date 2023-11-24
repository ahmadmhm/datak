<?php

namespace App\Http\Requests\V1;

use App\Enums\ArticleResource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchArticleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'source' => [
                'array', 'nullable', Rule::exists('sources', 'title'),
            ],
            'authors' => [
                'array', 'nullable',
            ],
            'dateFrom' => [
                'date', 'nullable',
            ],
            'dateTo' => [
                'date', 'nullable',
            ],
            'resource' => [
                'string', 'nullable', Rule::enum(ArticleResource::class),
            ],
        ];
    }
}
