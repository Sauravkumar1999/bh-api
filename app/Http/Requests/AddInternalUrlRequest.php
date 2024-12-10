<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddInternalUrlRequest extends FormRequest
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
            'url' => 'required|string',
            'type' => 'required|string|in:internal',
            'name' => 'nullable|string|max:255'
        ];
    }

    /**
     * Customize the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'url.required' => __('validation.url_required'),
            'url.url' => __('validation.url_invalid'),
            'type.required' => __('validation.type_required'),
            'type.in' => __('validation.type_invalid'),
            'name.max' => __('validation.name_max'),
        ];
    }
}
