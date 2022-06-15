<?php

namespace App\Http\Requests\Submissions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Entities\SubmissionCategory;
use App\Entities\Submission;

class UpdateSubmissionStatusRequest extends FormRequest
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
        Validator::extend('status', function ($attribute, $value, $parameters, $validator) {
            $submission = Submission::find($this->route('id'));
            return $submission->fullfilment == null && in_array($submission->status, array("PARTIAL PAID", "PAID", "PENDING", "PARTIAL APPROVED"));
        }, 'Submission Status can be Updated if status in PENDING, PARTIAL APPROVED, PARTIAL PAID or PAID And Fullfilment status is On Going');
        return [
            'status'    => ['status', Rule::in(['reject'])]
        ];
    }
}
