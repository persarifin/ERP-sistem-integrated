<?php

namespace App\Http\Requests\Submissions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Entities\Submission;

class UpdateSubmissionDueDateRequest extends FormRequest
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
            return $submission->fullfilment == null && in_array($submission->status, array("DRAFT","PENDING","PARTIAL APPROVED","APPROVED","PARTIAL PAID","PAID"));
        }, 'Submission can not be Updated if status in COMPLETED, CANCELLED, REJECTED, FAILED or REFUND And Fullfilment is not On Going');
        Validator::extend('greaten_than', function ($attribute, $value, $parameters, $validator) {
            $submission = Submission::find($this->route('id'));
            return $value > $submission->date;
        }, ':attribute field must be greater than Date');
        return [
            'due_date'  => 'required|date|greaten_than|status'
        ];
    }
}
