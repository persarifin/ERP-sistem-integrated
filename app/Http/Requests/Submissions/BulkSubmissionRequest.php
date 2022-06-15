<?php

namespace App\Http\Requests\Submissions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkSubmissionRequest extends FormRequest
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
        $rule_value = '';
        if ($this->input('field') == "category_id") {
            $rule_value = 'required|integer'; 
        }elseif ($this->input('field') == "status")  {
            $rule_value = 'nullable';
        }elseif ($this->input('field') == "fullfilment")  {
            $rule_value = 'required|boolean';
        }
        return [
            'field' => ['required', Rule::in(['category_id','status','fullfilment'])],
            'ids' => "required|array",
            'value' => $rule_value,
            'status' => ['string',Rule::requiredIf($this->input('value') == 0),Rule::in(['refund','cancel'])]
        ];
    }
}
