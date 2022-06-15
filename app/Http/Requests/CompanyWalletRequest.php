<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyWalletRequest extends FormRequest
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
        if ($this->method()== "PUT" || $this->method() == 'PATCH') {
            $rule_wallet = [
                'required','regex:/(^[a-zA-z-._ 0-9]+$)/', Rule::unique('company_wallets')->where(function ($query) {
                    return $query->where(['company_id' => $this->user()->company_id]);
                })->ignore($this->route('company_wallet'))
            ];
        }
        else {            
            $rule_wallet = [
                'required','regex:/(^[a-zA-z-._ 0-9]+$)/', Rule::unique('company_wallets')->where(function ($query) {
                    return $query->where(['company_id' => $this->user()->company_id]);
                })
            ];
        }
        return [
            'wallet_name' => $rule_wallet
        ];
    }
}
