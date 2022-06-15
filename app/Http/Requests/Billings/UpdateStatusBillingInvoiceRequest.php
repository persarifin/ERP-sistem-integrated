<?php

namespace App\Http\Requests\Billings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateStatusBillingInvoiceRequest extends FormRequest
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
        $billingInvoice = \App\Entities\BillingInvoice::find($this->route('id'));
        if (!$billingInvoice) {
            throw new HttpResponseException(response()->json([
                'success' => false,                            
                'message' => 'Billing Invoice not found',
            ], 404));
        }
        if(strpos($billingInvoice->invoice_name, '(RESIGN)')){
            throw new HttpResponseException(response()->json([
                'success' => false,                            
                'message' => 'The company has resigned',
            ], 404));
        }
        return [
            'company_wallet_id' => 'required|search:company_wallets,'.$this->input('company_wallet_id'),
            'amount'            => 'required|numeric|gt:0',
            'is_resign'         => 'max:1',
            'file'              => 'mimes:jpeg,bmp,png,jpg,pdf|max:1024',
        ];
    }
}
