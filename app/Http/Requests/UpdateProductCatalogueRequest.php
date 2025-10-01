<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductCatalogueRequest extends FormRequest
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
            'name' => 'required',
            'canonical' => [
                'required',
                Rule::unique('routers', 'canonical')
                    ->ignore($this->id, 'module_id'),
            ],

        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Bạn chưa nhập vào ô tiêu đề',
            'canonical.required' => 'Bạn chưa nhập vào ô duong dan',
            'canonical.unique' => 'Đường dẫn đã tồn tại hãy chọn đường dẫn khác',

        ];
    }
}
