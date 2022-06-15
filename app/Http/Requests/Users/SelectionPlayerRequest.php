<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class SelectionPlayerRequest extends FormRequest
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
            'full_name' => 'required|string',
            'phone' => 'required|digits_between:1,15',
            'profileImage' => 'required|mimes:jpg,jpeg,bmp,png|max:5024'
        ];
    }

    public function messages()
    {
        return $messages = [
            'profileImage.mimes' => 'Image or Document does not have valid extension!',
            'profileImage.max' => 'Maximum file size to upload is 1MB (1024 KB)'
        ];
    }
}
