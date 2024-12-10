<?php

namespace App\Http\Requests;

/**
 * @OA\Schema(
 *     schema="LoginRequest",
 *     type="object",
 *     required={"email", "password"},
 *     @OA\Property(property="email", type="string", description="email or telephone no"),
 *     @OA\Property(property="password", type="string"),
 * )
 */
class LoginRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email'        => 'required|string',
            'password'     => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'email.required'    => trans('user::auth.username_required'),
            'password.required' => trans('user::auth.password_required'),
        ];
    }
}
