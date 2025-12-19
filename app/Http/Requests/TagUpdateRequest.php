<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TagUpdateRequest extends FormRequest
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
                Rule::unique('blog_tags', 'slug')->ignore($this->route('tag'))
            ],
            'is_active' => ['boolean'],
        ];
    }
}
