<?php

namespace App\Http\Requests;

use App\DTOs\ArticleCategoryCreateDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArticleCategoryStoreRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:50'],
            'slug' => ['nullable', 'string', 'max:70', Rule::unique('article_categories', 'slug')],
            'parent_id' => ['nullable', 'integer', 'exists:article_categories,id'],
        ];
    }

    public function toCreateDto(): ArticleCategoryCreateDTO
    {
        return ArticleCategoryCreateDTO::fromArray($this->validated());
    }
}
