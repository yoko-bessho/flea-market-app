<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'profile_image' => ['file', 'mimes:jpeg,png'],
            'name' => ['required', 'string', 'max:20'],
            'postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address' => ['required', 'string'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'profile_image.mimes'  => 'プロフィール画像はjpegまたはpng形式で指定してください',
            'name.required' => 'お名前を入力してください',
            'name.max' => 'お名前は20文字以内で入力してください',
            'postal_code.required'  => '郵便番号を入力してください',
            'postal_code.regex'  => '郵便番号は半角のXXX-XXXXの形式で入力してください',
            'address.required'  => '住所を入力してください',
        ];
    }
}
