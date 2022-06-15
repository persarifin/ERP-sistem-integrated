<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Response;

class CompanyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if($this->method() == 'PATCH' || $this->method() == 'PUT'){
            $rule_email = 'required|string|email|max:255|unique:companies,email,'.$this->route('company');
            $rule_phone = 'required|digits_between:7,15|unique:companies,phone,'.$this->route('company');
        }else{
            $rule_email = 'required|string|email|max:255|unique:companies';
            $rule_phone = 'required|unique:companies|digits_between:7,15';
        }
        return [
            'email' => $rule_email,
            'phone' => $rule_phone,
            'business_name' => 'required',
            'legal_name' => 'string',
            'tax_number' => 'required',   
            'address' => 'string',
            'company_attachments.*.attachment_type'  => ['required', Rule::in(['COMPANY_LOGO', 'ESTABLISHMENT_DEED', 'REGISTRATION_CERTIFICATE', 'BUSINESS_LICENSE', 'TAX_NUMBER_PHOTO', 'COMPANY_PROFILE','MAIN_IMAGE','SOLUTION_IMAGE','OFFICE_IMAGE','TERMS_AND_CONDITION','PRIVACY_POLICY'])],
            'company_attachments.*.file'  => 'required|mimes:jpeg,bmp,png,jpg,pdf|max:5120',
        ];
    }
    public function messages()
    {
        return $messages = [
            'email.email' => ':attribute invalid format!, example@email.com',
            'phone.digits_between' => 'Invalid length phone number!'
        ];

    }
}
