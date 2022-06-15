<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
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
        if ($this->method() == "PUT" || $this->method() == 'PATCH') {
            return [
                'name' => ['required', 'string', 'max:30', 'search:roles,'.$this->route('role'),  Rule::unique('roles')->where(function ($query) {
                    return $query->where(['company_id' => $this->user()->company_id]);
                })->ignore($this->route('role'))],
                'permission_ids' => ['required', 'array'],
                'permission_ids.*' => ['numeric'],
            ];
        } else {
            return [
                'name' => ['required', 'string', 'max:30', Rule::unique('roles')->where(function ($query) {
                    return $query->where(['company_id' => $this->user()->company_id]);
                })],
                'permission_ids' => ['required', 'array'],
                'permission_ids.*' => ['numeric'],
            ];
        }
    }
    public function messages()
    {
        return $messages = [
            'search' => 'role not found'
        ];

    }
}
