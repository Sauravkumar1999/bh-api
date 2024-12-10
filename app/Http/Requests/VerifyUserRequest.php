<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyUserRequest extends FormRequest
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
        return [
            'phone' => 'required|string',
            'type' => 'required|string',
            'id' => $this->getEmailRule(),
            'otp' => 'required|string',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'phone.required' => __('validation.required', ['attribute' => __('validation.attributes.phone')]),
            'type.required' => __('validation.required', ['attribute' => __('validation.attributes.type')]),
            'id.required' => __('validation.required', ['attribute' => __('validation.attributes.id')]),
            'otp.required'     => __('validation.required', ['attribute' => __('validation.attributes.otp_code')]),
            // Add more custom error messages as needed
        ];
    }

    /**
     * Get the validation rules for the email field.
     *
     * @return array|string
     */
    private function getEmailRule()
    {
        if ($this->input('type') === 'find-password') {
            return 'required|email';
        }

        return 'nullable|email';
    }
}
