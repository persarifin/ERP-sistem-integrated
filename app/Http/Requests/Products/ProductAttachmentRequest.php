<?php

namespace App\Http\Requests\Products;
use Illuminate\Validation\Rule;

use Illuminate\Foundation\Http\FormRequest;

class ProductAttachmentRequest extends FormRequest
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
            'attachment_type' => ['required', Rule::in(['PRODUCT PHOTO', 'PRODUCT CONTENT'])],
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
