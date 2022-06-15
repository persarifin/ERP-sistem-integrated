<?php

namespace App\Http\Requests\Products;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Entities\Item;
use App\Entities\Product;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
        if ($this->input('product_name') == null) {
            $item = 1;
        }else{
            $item = Item::whereRaw("lower(item_name) = ?", $this->input('product_name'))->where(['company_id'=> $this->user()->company_id])->count();
        }
        if ($this->input('product_type') === 'TIME LIMIT') {
            $start = 'required|date_format:H:i';
            $end = 'required|date_format:H:i|after_or_equal:start';
        }elseif ($this->input('product_type') === 'LIMITED') {
            $start = 'required|date';
            $end = 'required|date|after_or_equal:start';
        }else {
            $start = 'nullable';
            $end = 'nullable';
        }

        if ($this->method() == "PUT" || $this->method() == "PATCH") {
            $product = Product::where(['id' => $this->route('product'), 'company_id' => $this->user()->company_id])->first();
            $role = $this->user()->roles->where('company_id', $this->user()->company_id)->first();
            if (empty($product)) {
                throw new HttpResponseException(response()->json([
                    'success' => false, 
                    'message' => 'product not found',                    
                ], 422));
            }
            elseif ($product->status == "APPROVED") {
                if (!$role->hasPermissionTo('Update Product After APPROVED')&& !$role->hasRole('super_enterprise')) {
                    throw new HttpResponseException(response()->json([
                        'success' => false,                            
                        'message' => 'User does not have the right permission to update Product after APPROVED',
                    ], 422));
                }
                $rule_name = [
                    'required','string','max:255', Rule::unique('products')->where(function ($query) {
                        return $query->where(['company_id' => $this->user()->company_id]);
                    })->ignore($this->route('product'))
                ];
            }else {
                $rule_name = [
                    'required','string','max:255' , Rule::unique('products')->where(function ($query) {
                        return $query->where(['company_id' => $this->user()->company_id]);
                    })->ignore($this->route('product'))
                ];
            }
            return [
                'product_name'          => $rule_name,
                'product_type'          => ['required', Rule::in(['NO LIMIT','TIME LIMIT','STOCK LIMIT','LIMITED'])],
                'min_stock'             => 'required|numeric|gt:0',
                'stock'                 => ['numeric', Rule::requiredIf($item == 0)],
                'buying_price'          => 'required|numeric|gt:0',
                'selling_price'         => 'required|numeric|min:0',
                'unit'                  => ['required', Rule::in(['Pcs','Box','Carton','Sachet','Hour','Day'])],
                'category_id'           => 'required|search:product_categories,'.$this->input('category_id'),
                // 'visibilities'          => 'required|array',
                // 'visibilities.*.interface_id' => 'required|numeric',
                'schedules.start'       => $start,
                'schedules.finish'      => $end
            ];
        }else {
            return [
                'product_name'          => [
                    'required','string','max:255' , Rule::unique('products')->where(function ($query) {
                        return $query->where(['company_id' => $this->user()->company_id]);
                    })
                ],
                'product_type'          => ['required', Rule::in(['NO LIMIT','TIME LIMIT','STOCK LIMIT','LIMITED'])],
                'min_stock'             => 'required|numeric|gt:0',
                'stock'                 => ['numeric', Rule::requiredIf($item == 0)],
                'buying_price'          => 'required|numeric|gt:0',
                'selling_price'         => 'required|numeric|min:0',
                'unit'                  => ['required', Rule::in(['Pcs','Box','Carton','Sachet','Hour','Day'])],
                'file'                  => 'required|mimes:jpeg,bmp,png,jpg,pdf|max:1024',
                'category_id'           => 'required|search:product_categories,'.$this->input('category_id'),
                'detail_images'         => 'array',
                'detail_images.*.file'  => 'mimes:jpeg,bmp,png,jpg,pdf|max:1024',
                // 'visibilities'          => 'required|array',
                // 'visibilities.*.interface_id' => 'required|numeric',
                'schedules.start'       => $start,
                'schedules.finish'      => $end
            ];
        }
       
    }
    public function message()
    {
        return $messages = [
            'file.mimes' => 'Image or Document does not have valid extension!',
            'file.max'   => 'Maximum file size to upload is 1MB (1024 KB)',
            'category_id.required' => 'category field is required',
            'detail_images.*.file.required'  => 'file field is required',
            'detail_images.*.file.mimes'     => 'Image or Document does not have valid extension!',
            'detail_images.*.file.max'       => 'Maximum file size to upload is 1MB (1024 KB)',
            'schedules.start' => 'start operating time is required',
            'schedules.finish' => 'finish operating time is required',
            'schedules.finish.after_or_equal' => 'finish must be greater than start'
        ];

    }
}
