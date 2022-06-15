<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendEmailRequest extends FormRequest
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
            'email' => 'required|email|max:50',
            'redirect_url' => 'required|string',
            'city' => 'string',
            'club' => 'string',
            'participants' => 'required|array',
            'participants.*' => ['required', 'string', Rule::in(['UNDANGAN', 'KAB/KOTA', 'KLUB'])]
        ];
    }
}
