<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="FindPasswordVerifyRequest",
 *     type="object",
 *     required={"name", "email", "phone", "otp"},
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="phone", type="string", example="+1234567890"),
 *     @OA\Property(property="otp", type="string", example="456789"),
 * )
 */

class FindPasswordVerifyRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email'  => 'required|email',
            'phone'  => 'required|min:10',
            'name'   => 'required|string',
            'otp'    => 'required|digits:6|numeric',
        ];
    }

    public function messages()
    {
        return [
            'email.email'        => __('validation.email', ['attribute' => __('validation.attributes.email')]),
            'phone.required'     => __('validation.required', ['attribute' => __('validation.attributes.phone')]),
            'phone.min'          => __('validation.min', ['attribute' => __('validation.attributes.phone'), 'min' => 10]),
            'name.required'      => __('validation.required', ['attribute' => __('validation.attributes.name')]),
            'otp.required'       => __('validation.required', ['attribute' => __('validation.attributes.otp')]),
            'otp.digits'         => __('validation.digits', ['attribute' => __('validation.attributes.otp'), 'digits' => 6]),
            'otp.numeric'         => __('validation.numeric', ['attribute' => __('validation.attributes.otp'),]),
        ];
    }
}
