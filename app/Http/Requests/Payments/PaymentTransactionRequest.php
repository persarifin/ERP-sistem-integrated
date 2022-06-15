<?php

namespace App\Http\Requests\Payments;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use App\Entities\Submission;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Entities\PaymentTransaction;

class PaymentTransactionRequest extends FormRequest
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
        Validator::extend('payment', function ($attribute, $value, $parameters, $validator) {
            $submission = Submission::where(['id'=>$this->input('submission_id'), 'company_id' => $this->user()->company_id])->first();
            if (!$submission) {
                throw new HttpResponseException(response()->json([
                    'success' => false,                            
                    'message' => 'Submission not found',
                ], 404));
            }
            $payment = PaymentTransaction::where('submission_id',$this->input('submission_id'))->sum('amount');
            $amount = $submission->amount - $payment;
            return $amount >= $value;
        }, 'Payment exceeds the bill amount');

        Validator::extend('status', function ($attribute, $value, $parameters, $validator) {
            $submission = Submission::where(['id'=>$this->input('submission_id'), 'company_id' => $this->user()->company_id])->first();
            if (!$submission) {
                throw new HttpResponseException(response()->json([
                    'success' => false,                            
                    'message' => 'Submission not found',
                ], 404));
            }
            return in_array($submission->status, array("APPROVED", "PARTIAL PAID"));
        }, 'Submission status must be APPROVED or PARTIAL PAID');
        
        return [
            'amount'            => 'required|payment|numeric|gt:0',
            'date'              => 'required|date',
            'company_wallet_id' => 'required|search:company_wallets,'. $this->input('company_wallet_id'),
            'file'              => 'mimes:jpeg,bmp,png,jpg,pdf|max:1024',
            'submission_id'     => 'required|status|search:submissions,'. $this->input('submission_id')
        ];
    }
    public function messages()
    {
        return $messages = [
            'submission_id.search' => 'submission not found',
            'company_wallet_id.search' => 'wallet not found',
        ];
    }
}
