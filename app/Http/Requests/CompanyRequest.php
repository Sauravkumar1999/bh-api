<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
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
            'name' => 'required|string',
            'url' => 'sometimes|nullable|string',
            'business_name' => 'sometimes|nullable|string',
            'representative_name' => 'sometimes|nullable|string', 
            'registration_number' => 'sometimes|nullable|string',
            'address' => 'sometimes|nullable|string',  
            'scope_of_disclosure' => 'sometimes|nullable|string',
            'registration_date' => 'sometimes|nullable|date',                                 
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('validation.required',['attribute' => __('validation.attributes.name')]),
            'url.required' => __('validation.required',['attribute' => __('validation.attributes.url')]),
            'business_name.required' => __('validation.required',['attribute' => __('validation.attributes.business_name')]),  
            'representative_name.required' => __('validation.required',['attribute' => __('validation.attributes.representative_name')]),
            'registration_number.required' => __('validation.required',['attribute' => __('validation.attributes.registration_number')]),
            'address.required' => __('validation.required',['attribute' => __('validation.attributes.address')]),  
            'scope_of_disclosure.required' => __('validation.required',['attribute' => __('validation.attributes.scope_of_disclosure')]),
            'registration_date.required' => __('validation.required',['attribute' => __('validation.attributes.registration_date')]),                                            
        ];
    }
}
