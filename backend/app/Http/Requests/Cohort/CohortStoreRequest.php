<?php

namespace App\Http\Requests\Cohort;

use Illuminate\Foundation\Http\FormRequest;

class CohortStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('cohorts.create') ?? true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'capacity' => ['sometimes', 'integer', 'min:1', 'max:5000'],
            'status' => ['sometimes', 'string', 'in:upcoming,active,completed,archived'],
            'settings' => ['sometimes', 'array'],
        ];
    }
}
