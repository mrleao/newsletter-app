<?php

namespace App\Http\Requests;

use App\DTOs\ArticleCategoryUpdateDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArticleCategoryUpdateRequest extends FormRequest
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
            'id' => ['required', 'integer', 'exists:article_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('article_categories', 'slug')->ignore($id)],
            'parent_id' => ['nullable', 'integer', 'exists:article_categories,id', 'not_in:' . $id],
        ];
    }

    public function toUpdateDto(): ArticleCategoryUpdateDTO
    {
        return ArticleCategoryUpdateDTO::fromArray($this->validated());
    }
}
