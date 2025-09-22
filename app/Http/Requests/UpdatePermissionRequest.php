<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Cho phép update
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'canonical' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'canonical')->ignore($this->language?->id ?? $this->id),
                // ignore chính bản ghi hiện tại để không báo trùng
            ],
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Bạn chưa nhập vào Ngôn ngữ',
            'name.string'   => 'Tên ngôn ngữ phải là chuỗi ký tự',
            'name.max'      => 'Tên ngôn ngữ không được vượt quá 255 ký tự',

            'canonical.required' => 'Bạn chưa nhập vào từ khóa của ngôn ngữ',
            'canonical.string'   => 'Từ khóa phải là chuỗi ký tự',
            'canonical.max'      => 'Từ khóa không được vượt quá 255 ký tự',
            'canonical.unique'   => 'Từ khóa đã tồn tại, vui lòng chọn ngôn ngữ khác',
        ];
    }
}
