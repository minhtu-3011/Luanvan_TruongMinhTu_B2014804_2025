<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|unique:users|max:191',
            'name' => 'string|required',
            'user_catalogue_id' => 'required|integer|gt:0',
            'password' => 'required|string|min:6',
            're_password' => 'required|string|same:password',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Bạn chưa nhập vào email',
            'email.email' => 'Email chưa đúng định dạng',
            'email.unique' => 'Email da ton tai',
            'email.string' => 'Email phai la dang ky tu',
            'email.max' => 'Email co do dai toi da 91',
            'name.required' => 'ban chua nhap ho ten',
            'name.string' => 'ho ten phai la dang ky tu',
            'user_catalogue_id.gt' => 'ban chua chon nhom thanh vien',
            'password.required' => 'Bạn chưa nhập vào mật khẩu',
            're_password.required' => 'ban phai nhap lai mat khau',
            're_password.same' => 'mat khau khong khop',


        ];
    }
}
