<?php

namespace App\Http\Requests\Payments;

use Illuminate\Foundation\Http\FormRequest;

class PaymentTransactionAttachmentRequest extends FormRequest
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
            'transaction_id' => 'required',
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
