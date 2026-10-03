<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class LessonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isCompleted = false;
        if (Auth::check()) {
            $isCompleted = $this->progress()
                ->where('user_id', Auth::id())
                ->where('status', 'completed')
                ->exists();
        }

        return [
            'id' => $this->id,
            'module_id' => $this->module_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content_type' => $this->content_type,
            'body_content' => $this->body_content,
            'video_url' => $this->video_url,
            'duration_minutes' => $this->duration_minutes,
            'order_index' => $this->order_index,
            'is_preview' => (bool) $this->is_preview,
            'is_published' => (bool) $this->is_published,
            'resources' => $this->resources,
            'is_completed' => $isCompleted,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
