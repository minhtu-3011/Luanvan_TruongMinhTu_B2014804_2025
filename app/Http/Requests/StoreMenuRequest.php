<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMenuRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {
        return [
            'menu_catalogue_id' => 'gt:0',
            'menu.name' => [
                'required',
            ]

        ];
    }

    public function messages(): array
    {
        return [
            'menu_catalogue_id' => 'Bạn chưa chọn vị trí menu.',
            'menu.name.required' => 'Bạn phải tạo ít nhất một menu.'
        ];
    }
}
