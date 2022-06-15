<?php

namespace App\Http\Requests\Contents;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ContentRequest extends FormRequest
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
            'content_name' => 'required|max:255',
            'content' => 'required',
            'date' => 'required',
            'status' => ['required',Rule::in(['DRAFT','PENDING','PUBLISHED','ARCHIVED'])],
            'category_id' => 'required'
        ];
    }
}
