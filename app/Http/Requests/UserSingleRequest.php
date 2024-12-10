<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class UserSingleRequest extends FormRequest
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

    protected function prepareForValidation()
    {
        $this->merge([
            'user_id' => $this->route('user_id'),
        ]);
    }
    public function rules()
    {
        return [
            'user_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $user = User::find($value);

                    if (!$user) {
                        $fail(__('validation.exists', ['attribute' => __('validation.attributes.user_id')]));
                    } elseif ($user->deleted_at !== null) {
                        $fail('The user has already been deleted.');
                    }
                },
            ],
        ];
    }

    public function messages()
    {
        return [
            'user_id.exists'    => __('validation.exists', ['attribute' => __('validation.attributes.user_id')]),
        ];
    }
}
