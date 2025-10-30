<?php

namespace App\Http\Requests;

use App\DTOs\ArticleCreateDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArticleStoreRequest extends FormRequest
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
        return [
            'category_id' => ['nullable', 'integer', 'exists:article_categories,id'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('articles', 'slug')],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'status' => ['nullable', 'in:draft,published'],
            'image' => ['nullable','file','mimetypes:image/jpeg,image/png,image/webp','max:2048'],

        ];
    }

    public function toCreateDto(): ArticleCreateDTO
    {
        return ArticleCreateDTO::fromArray($this->validated());
    }
}
