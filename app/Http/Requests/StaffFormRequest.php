<?php

namespace App\Http\Requests;

use App\Services\EncryptionService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StaffFormRequest extends FormRequest
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
        $id = trim(EncryptionService::decrypt($this->input('enc_id'),ID_ENCRYPTION_KEY));
        return [
            'full_name' => ['required'],
            'first_name' => ['required'],
            'init_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['sometimes'],
            'nic' => ['required'],
            'designation' => ['required'],
            'mobile' => ['required'],
            'phone' => ['sometimes'],
            'department' => ['required'],
            'emp_no' => ['sometimes'],
            'address_1' => ['sometimes'],
            'address_2' => ['sometimes'],
            // 'image' => ['sometimes','image'],
            'district' => ['sometimes'],
        ];
    }
}
