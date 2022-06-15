<?php

namespace App\Http\Requests\Items;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
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
            'item_name'     => 'required',
            'buying_price'  => 'numeric|gt:0',
            'selling_price' => 'numeric|gt:0',
            'submission_id' => 'required',
            'product_id'    => 'required'
        ];
    }
}
