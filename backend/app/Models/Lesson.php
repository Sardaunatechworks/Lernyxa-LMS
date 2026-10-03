<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'module_id',
        'title',
        'slug',
        'content_type',
        'body_content',
        'video_url',
        'duration_minutes',
        'order_index',
        'is_preview',
        'is_published',
        'resources',
    ];

    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
            'order_index' => 'integer',
            'is_preview' => 'boolean',
            'is_published' => 'boolean',
            'resources' => 'array',
        ];
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }
}
