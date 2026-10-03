<?php

namespace App\Http\Requests\Curriculum;

use Illuminate\Foundation\Http\FormRequest;

class LessonStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('courses.edit') ?? true;
    }

    public function rules(): array
    {
        return [
            'module_id' => ['required', 'exists:modules,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255'],
            'content_type' => ['sometimes', 'string', 'in:video,article,quiz,assignment'],
            'body_content' => ['nullable', 'string'],
            'video_url' => ['nullable', 'string'],
            'duration_minutes' => ['sometimes', 'integer', 'min:0'],
            'order_index' => ['sometimes', 'integer', 'min:1'],
            'is_preview' => ['sometimes', 'boolean'],
            'is_published' => ['sometimes', 'boolean'],
            'resources' => ['sometimes', 'array'],
        ];
    }
}
