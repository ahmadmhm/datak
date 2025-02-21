<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'source' => [
                'string', 'nullable',
            ],
            'context' => [
                'string', 'nullable',
            ],
            'dateFrom' => [
                'date', 'nullable',
            ],
            'dateTo' => [
                'date', 'nullable',
            ],
        ];
    }
}
