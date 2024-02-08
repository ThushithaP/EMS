<?php

namespace App\Http\Requests;

use App\Services\EncryptionService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepartmentFormRequest extends FormRequest
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
    public function rules(): array
    {
        $id = trim(EncryptionService::decrypt($this->input('enc_depid'), ID_ENCRYPTION_KEY));
        return [
            'dep_name' => ['required',
                Rule::unique('departments','dep_name')->ignore($id)
            ],
            'dep_email' => [
                'required',
                'email',
                Rule::unique('departments', 'dep_email')->ignore($id),
            ],
            'dep_desc' => 'sometimes',
            'dep_status' => 'sometimes',
        ];
    }

    public function messages() : array {
        return [
            'dep_name.required' => 'The department name is required.',
            'dep_email.required' => 'The department email is required.',
            'dep_email.unique' => 'The department email is already added.',
            'dep_name.unique' => 'The department name is already added.',
        ];
    }

    public $validator = null;
    protected function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }

}
