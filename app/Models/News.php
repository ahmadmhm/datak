<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Scout\Searchable;

class News extends Model
{
    use HasFactory, Notifiable, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'date',
        'source',
        'text',
        'link',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function create(array $data, $id = null)
    {
        // Use the provided id or the id from data if available.
        $id = $id ?? ($data['id'] ?? null);

        return $this->elasticService->indexDocument($this->index, $id, $data);
    }

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'date' => $this->date,
            'source' => $this->source,
            'text' => $this->text,
            'link' => $this->link,
        ];
    }
}
