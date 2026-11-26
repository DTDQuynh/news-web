<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|integer|exists:categories,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug',
            'summary' => 'nullable|string|max:500',
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:draft,published',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Vui lòng chọn danh mục cho bài viết.',
            'category_id.exists' => 'Danh mục được chọn không tồn tại.',
            'title.required' => 'Vui lòng nhập tiêu đề bài viết.',
            'title.max' => 'Tiêu đề bài viết không được vượt quá 255 ký tự.',
            'slug.max' => 'Slug không được vượt quá 255 ký tự.',
            'slug.unique' => 'Slug bài viết này đã tồn tại.',
            'summary.max' => 'Tóm tắt không được vượt quá 500 ký tự.',
            'content.required' => 'Vui lòng nhập nội dung bài viết.',
            'thumbnail.image' => 'Tệp tải lên phải là hình ảnh.',
            'thumbnail.mimes' => 'Ảnh bìa chỉ chấp nhận định dạng jpg, jpeg, png hoặc webp.',
            'thumbnail.max' => 'Dung lượng ảnh bìa tối đa là 2MB.',
            'status.required' => 'Vui lòng chọn trạng thái bài viết.',
            'status.in' => 'Trạng thái bài viết không hợp lệ.',
        ];
    }
}