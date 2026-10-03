<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CohortResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $enrolledCount = $this->enrollments()->where('status', '!=', 'dropped')->count();
        $availableSlots = max(0, $this->capacity - $enrolledCount);

        $myEnrollment = null;
        if (Auth::check()) {
            $enrollment = $this->enrollments()->where('user_id', Auth::id())->first();
            if ($enrollment) {
                $myEnrollment = [
                    'status' => $enrollment->status,
                    'enrolled_at' => $enrollment->enrolled_at?->toIso8601String(),
                    'progress_percentage' => (float) $enrollment->progress_percentage,
                    'final_grade' => $enrollment->final_grade ? (float) $enrollment->final_grade : null,
                ];
            }
        }

        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'capacity' => $this->capacity,
            'enrolled_count' => $enrolledCount,
            'available_slots' => $availableSlots,
            'is_full' => $availableSlots <= 0,
            'status' => $this->status,
            'settings' => $this->settings,
            'courses' => CourseResource::collection($this->whenLoaded('courses')),
            'my_enrollment' => $myEnrollment,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
