<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="VerifyPhoneRequest",
 *     type="object",
 *     required={"phone"},
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="phone", type="string", example="+1234567890"),
 *     @OA\Property(property="type", type="string", enum={"find-password", "find-id"}),
 * )
 */

class VerifyPhoneRequest extends FormRequest
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
            'name'   => $this->getNameRule(),
            'email'  => $this->getEmailRule(),
            'phone'  => 'required|min:10',
            'type'   => 'nullable',
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
    /**
     * Get the validation rules for the name field.
     *
     * @return array|string
     */
    private function getNameRule()
    {
        if ($this->input('type') === 'find-password') {
            return 'required|string';
        }

        return 'nullable|string';
    }
    public function messages()
    {
        return [
            'email.email'        => __('validation.email', ['attribute' => __('validation.attributes.email')]),
            'phone.required'     => __('validation.required', ['attribute' => __('validation.attributes.phone')]),
            'phone.min'          => __('validation.min', ['attribute' => __('validation.attributes.phone'), 'min' => 10]),
            'name.required'      => __('validation.required', ['attribute' => __('validation.attributes.name')]),
        ];
    }
}
