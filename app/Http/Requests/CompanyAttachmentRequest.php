<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Response;

class CompanyAttachmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'attachment_type' => ['required', Rule::in(['COMPANY_LOGO', 'ESTABLISHMENT_DEED', 'REGISTRATION_CERTIFICATE', 'BUSINESS_LICENSE', 'TAX_NUMBER_PHOTO', 'COMPANY_PROFILE','MAIN_IMAGE','SOLUTION_IMAGE','OFFICE_IMAGE','TERMS_AND_CONDITION','PRIVACY_POLICY'])],
            'file' => 'required|mimes:jpeg,bmp,png,jpg,pdf|max:1024'
        ];
    }

    public function messages()
    {
      return $messages = [
        'file.mimes' => 'Image or Document does not have valid extension!',
        'file.max' => 'Maximum file size to upload is 1MB (1024 KB)'
      ];
    }
}
