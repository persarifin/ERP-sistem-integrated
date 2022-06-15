<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if($this->input('type') == 'COMPANY'){
            return [
                'full_name' => 'required|max:255|regex:/([a-zA-z]+$)/',
                'username' => 'required|min:8|unique:users|regex:/(^[a-zA-z._0-9]+$)/',
                'email' => 'required|string|email|max:255|unique:users|unique:companies',
                'phone' => 'required|digits_between:7,15|unique:users|unique:companies',            
                'password' => 'required|min:5|confirmed',
                'type' => ['required', Rule::in(['PERSONAL','COMPANY'])],
                'business_name' => 'required',
                'legal_name' => 'required',
                'tax_number' => 'required',    
                'address' => 'required',
                'subdistrict' => 'nullable',
                'city' => 'nullable',
                'province' => 'nullable',
                'postal_code' => 'nullable',
                'country' => 'nullable',
                'bio' => 'nullable',
                'tagline' => 'nullable',
                'sub_tagline' => 'nullable',
                'vision' => 'nullable',
                'mission' => 'nullable',
                'work_culture' => 'nullable',
                'working_space' => 'nullable'
            ];
        }
        else{
            return [
                'full_name' => 'required|max:255|regex:/([a-zA-z]+$)/',
                'username' => 'required|min:8|unique:users|regex:/(^[a-zA-z._0-9]+$)/',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'required|unique:users|digits_between:7,15',            
                'password' => ['required','min:5','confirmed', Password::min(8)
                                                                    ->mixedCase()
                                                                    ->letters()
                                                                    ->numbers()
                                                                    ->uncompromised(),
                                                                ],
                'type' => ['required', Rule::in(['PERSONAL','COMPANY'])]
            ];
        }
        
    }
    public function messages()
    {
        return $messages = [
            'full_name.regex' => 'The format :attribute field only text',
            'email.email' => ':attribute invalid format!, example@email.com',
            'email.unique' => ':attribute do not match',
            'username.regex' => 'Invalid character! :attribute only use . or _ example_user.01',
            'username.unique' => ':attribute not available',
            'username.min' => ':attribute must be at least 8 character',
        ];

    }
}
