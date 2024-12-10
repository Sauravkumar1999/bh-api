<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 *     schema="MonthlyNewsRequest",
 *     type="object",
 *     required={"detail", "form", "posting_date"},
 *     @OA\Property(property="detail", type="string", example="Testing Detail"),
 *     @OA\Property(property="form", type="string", example="Testing Form String"),
 *     @OA\Property(property="posting_date", type="date", example="2024-10-15")
 * )
 */

class MonthlyNewsRequest extends RequestExceptionModifier
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
            'detail' => 'required|string',
            'form' => 'required|string',
            'posting_date' => 'required|date'
        ];
    }

    public function messages()
    {
        return [
            'detail.required' => __('validation.required', ['attribute' => __('validation.attributes.detail')]),
            'form.required' => __('validation.required', ['attribute' => __('validation.attributes.form')]),
            'posting_date.required' => __('validation.required', ['attribute' => __('validation.attributes.posting_date')])
        ];
    }
}
