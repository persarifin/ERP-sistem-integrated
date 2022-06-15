<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
        if($this->method() == 'PATCH' || $this->method() == 'PUT'){
            $rule_email = 'required|string|email|max:255|unique:users,email,'. $this->route('user');
            $rule_username = 'required|min:8|regex:/(^[a-zA-z._0-9]+$)/|unique:users,username,'. $this->route('user');
            $rule_phone = 'required|digits_between:7,15|unique:users,phone,'. $this->route('user');
        }
        else{
            $rule_email = 'required|string|email|max:255|unique:users,email|unique:companies,email';
            $rule_username = 'required|min:8|unique:users,username|regex:/(^[a-zA-z._0-9]+$)/';
            $rule_phone = 'required|unique:users,phone|digits_between:7,15|unique:companies,phone';
        }
        return [
            'full_name' => 'required|max:255',
            'username' => $rule_username,
            'email' => $rule_email,
            'phone' => $rule_phone,
            'role_id' => 'required',
            'type' => ['required', Rule::in(['PERSONAL','COMPANY'])],
            'file' => 'mimes:jpeg,bmp,png,jpg,pdf|max:1024',
            'attachments.*.file'  => 'mimes:jpeg,bmp,png,jpg,pdf|max:1024',
            'attachments.*.attachment_type' => ['string', Rule::in(['ID CARD PHOTO', 'SELFIE PHOTO', 'TAX NUMBER PHOTO'])],
            'business_name' => [Rule::requiredIf($this->input('type') === "COMPANY")],
            'legal_name' => [Rule::requiredIf($this->input('type') === "COMPANY")],
            'tax_number' => [Rule::requiredIf($this->input('type') === "COMPANY")],
            'address' => [Rule::requiredIf($this->input('type') === "COMPANY")],
            'reseller' => [Rule::requiredIf($this->input('type') === "COMPANY")],
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
            'working_space' => 'nullable',
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
