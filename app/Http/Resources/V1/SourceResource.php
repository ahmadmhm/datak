<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class SourceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'title' => $this->title,
        ];
    }
}
