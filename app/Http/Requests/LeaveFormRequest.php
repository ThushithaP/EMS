<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeaveFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            'leave_type' => ['required'],
            'leave_from' => ['required'],
            'report_date' => ['required'],
            'days' => ['required'],
            'supervisor' => ['required'],
            'leave_to' => ['required'],
            'res_person' => ['required'],
            'reason' => ['required'],           
        ];
    }
}
