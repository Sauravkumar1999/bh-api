<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="FCMTokenRequest",
 *     type="object",
 *     required={"uuid", "fcm_token"},
 *     @OA\Property(property="uuid", type="string"),
 *     @OA\Property(property="fcm_token", type="string")
 * )
 */
class FCMTokenRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'uuid'    => 'required|string',
            'fcm_token' => 'required|string|unique:device_tokens',
        ];
    }

    public function messages()
    {
        return [
            'uuid.required'    => __('validation.required', ['attribute' => __('validation.attributes.uuid')]),
            'fcm_token.required' => __('validation.required', ['attribute' =>__('validation.attributes.fcm_token')]),
            'fcm_token.unique' => __('validation.unique', ['attribute' =>__('validation.attributes.fcm_token')]),
        ];
    }
}
