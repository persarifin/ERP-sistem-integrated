<?php

namespace App\Http\Requests\Payments;

use Illuminate\Foundation\Http\FormRequest;
use App\Entities\CompanyWallet;
use Illuminate\Support\Facades\Validator;


class PaymentReconciliationRequest extends FormRequest
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

        Validator::extend('balance', function ($attribute, $value, $parameters, $validator) {
            $query = CompanyWallet::find($this->input('from_wallet_id'));
            return $query ? $query->balance : 0 > $value;
        }, 'the balance is not sufficient');

        return [
            'amount'            => 'required|balance|numeric|min:0|not_in:0',
            'from_wallet_id'    => 'required',
            'to_wallet_id'      => 'required|not_in:,'. $this->input('from_wallet_id')
        ];
    }
    public function messages()
    {
        return $messages = [
            'amount.not_in'         => ':attribute field must be greater than zero',
            'amount.numeric'        => ':attribute field must be a number',
            'amount.min'            => ':attribute field cannot be less than zero',
            'to_wallet_id.not_in'   => 'Wallet To field cannot same with Wallet From field'
        ];
    }
}
