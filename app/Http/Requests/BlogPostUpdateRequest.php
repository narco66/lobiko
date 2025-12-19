<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlogPostUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('blog.update') ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('articles', 'slug')->ignore($this->route('post'))
            ],
            'excerpt' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'category_id' => ['nullable', 'integer', 'exists:article_categories,id'],
            'tags' => ['array'],
            'tags.*' => ['integer', 'exists:blog_tags,id'],
            'status' => ['required', 'in:draft,review,published,archived'],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:300'],
            'canonical_url' => ['nullable', 'url'],
        ];
    }
}
