<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class FilterRequest extends FormRequest
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
     * add all the filter related request here.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'code'                          => 'sometimes|nullable|string',
            'product_name'                  => 'sometimes|nullable|string',
            'per_page'                      => 'sometimes|nullable|integer|min:1|max:100',
            'status'                        => 'sometimes|nullable|boolean',
            'role'                          => 'sometimes|nullable|string',
            'royal_member_application'      => 'sometimes|nullable|string',
            'confirm_start_date'            => [
                'nullable',
                'string',
                'date',
                Rule::requiredIf(function () {
                    return $this->filled('confirm_end_date');
                })
            ],
            'confirm_end_date'              => [
                'nullable',
                'string',
                'date',
                Rule::requiredIf(function () {
                    return $this->filled('confirm_start_date');
                })
            ],
        ];
    }


    /**
     * add request filter here also
     *
     * @return array
     */
    public function filters()
    {
        return $this->only([
            'code',
            'product_name',
            'per_page',
            'status',
            'role',
            'royal_member_application',
            'type'
        ]);
    }
}
