<?php

namespace App\Http\Requests\Submissions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Entities\Submission;

class UpdateFullfilmentRequest extends FormRequest
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
        $submission = Submission::where(['id' => $this->route('id'), 'company_id' => Auth::user()->company_id])->first();
        if (empty($submission)) {
            throw new HttpResponseException(response()->json([
                'success' => false,                            
                'message' => 'Submission not found',
            ], 404));
        }
        Validator::extend('submission_status', function ($attribute, $value, $parameters, $validator) {
            $submission = Submission::find($this->route('id'));
            return in_array($submission->status, array("APPROVED","PARTIAL PAID", "PAID","COMPLETED")) && $submission->fullfilment == null;
        }, 'Submission can be Updated if status in APPROVED, PARTIAL PAID, PAID AND COMPLETED');
        return [
            'status'   => ['submission_status', Rule::in(['cancel', 'refund'])]
        ];
    }
}
