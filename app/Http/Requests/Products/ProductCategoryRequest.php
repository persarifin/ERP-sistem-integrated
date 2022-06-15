<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductCategoryRequest extends FormRequest
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
        if ($this->method() == "PUT" || $this->method() == "PATCH") {
            $rule_name = [
                'required','string','max:255', 'search:product_categories,'. $this->route('product_category'), Rule::unique('product_categories')->where(function ($query) {
                    return $query->where(['company_id' => $this->user()->company_id]);
                })->ignore($this->route('product_category'))
            ];
        }
        else {
            $rule_name = [
                'required','string','max:255' , Rule::unique('product_categories')->where(function ($query) {
                    return $query->where(['company_id' => $this->user()->company_id]);
                })
            ];
        }
        return [
            'category_name' => $rule_name
        ];
    }
}
