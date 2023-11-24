<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'resource' => $this->load_resource->value,
            'source' => new SourceResource($this->whenLoaded('source')),
            'author' => $this->author,
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,
            'url' => $this->url,
            'urlToImage' => $this->url_to_image,
            'publishedAt' => $this->published_at,
        ];
    }
}
