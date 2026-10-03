<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class CourseStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('courses.create') ?? true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'thumbnail_url' => ['nullable', 'url'],
            'difficulty_level' => ['sometimes', 'string', 'in:beginner,intermediate,advanced'],
            'status' => ['sometimes', 'string', 'in:draft,published,archived'],
            'settings' => ['sometimes', 'array'],
        ];
    }
}
