<?php

namespace App\Http\Requests\Users;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'full_name' => 'required|max:255|regex:/([a-zA-z]+$)/',
            'email' => 'required|string|email|max:255|unique:users,email,'. $this->user()->id,
            'username' => 'required|min:8|regex:/(^[a-zA-z._0-9]+$)/|unique:users,username,'. $this->user()->id,
            'phone' => 'required|digits_between:7,15|unique:users,phone,'. $this->user()->id,
            'attachments.*.file'  => 'mimes:jpeg,bmp,png,jpg,pdf|max:1024',
            'attachments.*.attachment_type' => ['string', Rule:: in(['ID CARD PHOTO', 'SELFIE PHOTO', 'TAX NUMBER PHOTO', 'PHOTO PROFILE'])],
        ];
    }
    public function messages()
    {
        return $messages = [
            'full_name.regex' => 'The format :attribute field only text',
            'email.email' => ':attribute invalid format, example@email.com',
            'email.unique' => ':attribute do not match',
            'username.regex' => 'Invalid character! :attribute only use . or _ example_user.01',
            'username.unique' => ':attribute not available',
            'username.min' => ':attribute must be at least 8 character',
        ];

    }
}
