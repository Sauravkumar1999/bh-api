<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="ReferralCodeRequest",
 *     type="object",
 *     required={"referral_code"},
 *     @OA\Property(property="referral_code", type="string")
 * )
 */
class ReferralCodeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'referral_code' => 'nullable|exists:users,code',
        ];
    }
}

