<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    public function toArray($request)
    {
        $resource = $this->resource['_source'];

        return [
            'id' => $this['_id'],
            'text' => $resource['text'],
            'post_date' => $resource['post_date'] ?? $resource['date'],
            'user' => $resource['poster_username'] ?? '',
            'link' => $resource['post_link'] ?? $resource['link'],
            'type' => $resource['type'] ?? 'text',
            'source' => $resource['source'] ?? $this->resource['_index'],
        ];
    }
}
