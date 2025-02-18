<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="RefreshRequest",
 *     type="object",
 *     required={"refresh_token"},
 *     @OA\Property(property="refresh_token", type="string"),
 * )
 */
class RefreshRequest extends FormRequest
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
            'refresh_token'        => 'required|string',
        ];
    }
}
