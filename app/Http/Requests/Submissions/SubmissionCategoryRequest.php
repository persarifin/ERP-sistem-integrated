<?php

namespace App\Http\Requests\Submissions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class SubmissionCategoryRequest extends FormRequest
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
        if ($this->method() == "PUT" || $this->method() == "PATCH" ) {
            $rule_category_name = [
                'required','string','max:255', 'search:submission_categories,'. $this->route('submission_category') , Rule::unique('submission_categories')->where(function ($query) {
                    return $query->where(['company_id' => $this->user()->company_id]);
                })->ignore($this->route('submission_category'))
            ];
        }
        else {
            $rule_category_name = [
                'required','string','max:255' , Rule::unique('submission_categories')->where(function ($query) {
                    return $query->where(['company_id' => $this->user()->company_id]);
                })
            ];
        }
        return [
            'category_name'     => $rule_category_name,
            'maximum'           => 'required|numeric|gte:0',
            'submission_type'   => ['required', Rule::in(['INCOME', 'EXPENSE'])],
            // company_id 
        ];
    }
}
