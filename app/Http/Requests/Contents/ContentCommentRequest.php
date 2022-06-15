<?php

namespace App\Http\Requests\Contents;

use Illuminate\Foundation\Http\FormRequest;

class ContentCommentRequest extends FormRequest
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
            'comment' => 'required',
            'content_id' => 'required',
            'date' => 'required'
            //userid
        ];
    }
}
