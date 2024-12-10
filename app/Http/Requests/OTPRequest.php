<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="OTPRequest",
 *     type="object",
 *     required={"phone", "user_id", "otp"},
 *     @OA\Property(property="phone", type="string"),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(property="otp", type="string")
 * )
 */
class OTPRequest extends FormRequest
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

        if ($this->routeIs('auth.send_otp')) {
            return [
                'phone' => 'required|string',
                'type' => 'required|string',
                'id' => $this->getEmailRule(),
                'name' => 'required|string'
            ];
        } else {
            return [
                'user_id' => 'required|integer',
                'otp' => 'required|string',
            ];
        }
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
            'user_id.required'      => __('validation.required', ['attribute' => __('validation.attributes.user_id')]),
            'otp.required'     => __('validation.required', ['attribute' => __('validation.attributes.otp_code')]),
            'type.required' => __('validation.required', ['attribute' => __('validation.attributes.type')]),
            'id.required' => __('validation.required', ['attribute' => __('validation.attributes.id')]),
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
