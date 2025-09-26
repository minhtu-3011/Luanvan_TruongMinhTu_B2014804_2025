<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TranslateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'translate_name' => 'required|string|max:255',
            'translate_canonical' => [
                'required',
                Rule::unique('routers', 'canonical')
                    ->ignore($this->id, 'module_id'),
            ],
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'translate_name.required' => 'Bạn chưa nhập vào Ngôn ngữ',
            'translate_name.string' => 'Tên ngôn ngữ phải là chuỗi ký tự',
            'translate_name.max' => 'Tên ngôn ngữ không được vượt quá 255 ký tự',

            'translate_canonical.required' => 'Bạn chưa nhập vào từ khóa của ngôn ngữ',
            'translate_canonical.string' => 'Từ khóa phải là chuỗi ký tự',
            'translate_canonical.max' => 'Từ khóa không được vượt quá 255 ký tự',
            'translate_canonical.unique' => 'Từ khóa đã tồn tại, vui lòng chọn ngôn ngữ khác',
        ];
    }
}
