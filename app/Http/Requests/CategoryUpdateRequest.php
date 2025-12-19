<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('blog.update') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('article_categories', 'slug')->ignore($this->route('category'))
            ],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'integer', 'exists:article_categories,id'],
            'is_active' => ['boolean'],
        ];
    }
}
