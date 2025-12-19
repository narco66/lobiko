<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MediaUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('blog.media.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'max:5120'], // 5MB
            'alt_text' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:255'],
        ];
    }
}
