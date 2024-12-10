<?php

namespace App\Http\Requests;

use App\Rules\PasswordRequirement;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="FindPasswordResetRequest",
 *     type="object",
 *     required={"password","password_confirmation"},
 *     @OA\Property(property="password", type="string", minLength=8),
 *     @OA\Property(property="password_confirmation", type="string", minLength=8),
 * )
 */

class FindPasswordResetRequest extends FormRequest
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
            'password' => ['required', 'min:8', 'string', 'confirmed', new PasswordRequirement()],
        ];
    }
    public function messages()
    {
        return [
            'password.required'  => __('validation.required', ['attribute' => __('validation.attributes.password')]),
            'password.min'       => __('validation.min', ['attribute' => __('validation.attributes.password'), 'min' => 8]),
            'password.string'    => __('validation.string', ['attribute' => __('validation.attributes.password')]),
            'password.confirmed' => __('validation.confirmed', ['attribute' => __('validation.attributes.password')]),
        ];
    }
}
