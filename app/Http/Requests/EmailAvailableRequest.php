<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="EmailAvailableRequest",
 *     type="object",
 *     required={"email"},
 *     @OA\Property(property="email", type="string")
 * )
 */
class EmailAvailableRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email|unique:users,email',
        ];
    }

}
