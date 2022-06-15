<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Http\Response;
use DB;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return $rules = [
            'email' => 'required|regex:/(^[a-zA-z._@0-9]+$)/',
            'password' => 'required',
            'interface' => 'required'
        ];
    }
    public function messages()
    {
        return $messages = [
            'email.required' => 'The Email or phone or Username fields is required',
            'email.regex' => 'The Email or phone or Username was invalid input'
        ];

    }
}
