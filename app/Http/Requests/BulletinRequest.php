<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulletinRequest extends FormRequest
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
            'title'         => 'required',
            'distinguish'   => 'required',
            'permission'    => 'nullable',
            'content'       => 'required'
        ];
    }

    public function messages()
    {
        return [
            'title.required'         => __('messages.title_require'),
            'distinguish.required'   => __('messages.distinguish_require'),
            'permission.required'    => __('messages.permission_require'),
            'content.required'       => __('messages.content_require')
        ];
    }
}
