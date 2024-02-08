<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileFormRequest extends FormRequest
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
        return [
            'full_name' => ['required'],
            'first_name' => ['required'],
            'init_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['sometimes'],
            'nic' => ['required'],
            'mobile' => ['required'],
            'phone' => ['sometimes'],
            'address_1' => ['sometimes'],
            'address_2' => ['sometimes'],
            'image' => ['sometimes','image'],
            'district' => ['sometimes'],
        ];
    }
}
