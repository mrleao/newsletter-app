<?php

namespace App\Http\Requests;

use App\DTOs\ArticleUpdateDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArticleUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = (int) $this->input('id');

        return [
            'id' => ['required', 'integer', 'exists:articles,id'],
            'category_id' => ['sometimes', 'nullable', 'integer', 'exists:article_categories,id'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('articles', 'slug')->ignore($id)],
            'title' => ['sometimes', 'string', 'max:255'],
            'body' => ['sometimes', 'string'],
            'status' => ['sometimes', 'in:draft,published'],
            'published_at' => ['sometimes', 'nullable', 'date'],
            'image' => ['nullable', 'file', 'mimetypes:image/jpeg,image/png,image/webp', 'max:2048'],
        ];
    }

    public function toUpdateDto(): ArticleUpdateDTO
    {
        return ArticleUpdateDTO::fromArray($this->validated());
    }
}
