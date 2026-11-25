<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $category = $this->route('category');
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories', 'name')->ignore($category),
            ],
            'slug' => [
                'nullable',
                'string',
                'max:120',
                Rule::unique('categories', 'slug')->ignore($category),
            ],
            'description' => 'nullable|string|max:500',
        ];
    }
    
    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'name.max' => 'Tên danh mục không được vượt quá 100 ký tự.',
            'name.unique' => 'Tên danh mục này đã tồn tại.',
            'slug.max' => 'Slug không được vượt quá 120 ký tự.',
            'slug.unique' => 'Slug này đã được sử dụng.',
            'description.max' => 'Mô tả danh mục không được vượt quá 500 ký tự.',
        ];
    }
}
