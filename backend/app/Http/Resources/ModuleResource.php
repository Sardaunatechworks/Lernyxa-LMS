<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'title' => $this->title,
            'description' => $this->description,
            'order_index' => $this->order_index,
            'unlock_offset_days' => $this->unlock_offset_days,
            'is_published' => (bool) $this->is_published,
            'lessons' => LessonResource::collection($this->whenLoaded('lessons')),
            'lessons_count' => $this->lessons_count ?? $this->lessons()->count(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
