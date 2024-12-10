<?php

    namespace App\Http\Requests;

    use App\Rules\PhoneNumber;
    use App\Rules\PasswordRequirement;
    use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

    /**
     * @OA\Schema(
     *     schema="RegisterRequest",
     *     type="object",
     *     required={"first_name", "email", "password", "password_confirmation", "phone"},
     *     @OA\Property(property="first_name", type="string"),
     *     @OA\Property(property="email", type="string", format="email"),
     *     @OA\Property(property="password", type="string", minLength=8),
     *     @OA\Property(property="password_confirmation", type="string", minLength=8),
     *     @OA\Property(property="phone", type="string", example="+1234567890"),
     *     @OA\Property(property="dob", type="string", format="date", example="2000-01-01"),
     *     @OA\Property(property="gender", type="string", enum={"male", "female", "other"}),
     *     @OA\Property(property="account_number", type="string"),
     *     @OA\Property(property="bank_id", type="string"),
     *     @OA\Property(property="referral_code", type="string"),
     *     @OA\Property(property="referral_code_verified", type="string"),
     *     @OA\Property(property="post_code", type="string"),
     *     @OA\Property(property="address", type="string"),
     *     @OA\Property(property="address_detail", type="string"),
     *     @OA\Property(
     *         property="id_photo",
     *         type="string",
     *         format="binary",
     *         description="ID photo file. Accepts only jpeg, png, jpg, gif"
     *     ),
     *     @OA\Property(
     *         property="bankbook_photo",
     *         type="string",
     *         format="binary",
     *         description="Bankbook photo file. Accepts only jpeg, png, jpg, gif"
     *     )
     * )
     */
    class RegisterRequest extends FormRequest
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
            $rules = [
                'email'                  => 'required|email|unique:users,email',
                'first_name'             => 'required|string|max:255',
                'password'               => ['required', 'string', 'confirmed', new PasswordRequirement()],
                'phone'                  => ['required', 'unique:contacts,telephone_1', new PhoneNumber],
                'post_code'              => 'nullable|string|max:10',
                'address'                => 'nullable|string|max:255',
                'dob'                    => 'nullable',
                'address_detail'         => 'nullable|string|max:255',
                'referral_code'          => 'nullable|string|max:20|exists:users,code',
                'id_photo'               => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'bankbook_photo'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'bank_id'                => 'nullable|exists:banks,id',
                'account_number'         => 'nullable|string|max:20',
                'gender'                 => 'nullable|string',
            ];


            if (!empty($this->input('referral_code'))) {
                $rules['referral_code_verified'] = [
                    'nullable',
                    'string',
                    'max:20',
                    'same:referral_code'
                ];
            }

            return $rules;
        }

        public function messages()
        {
            return [
                'password.required'  => __('validation.required', ['attribute' => __('validation.attributes.password')]),
                'password.min'       => __('validation.min', ['attribute' => __('validation.attributes.password'), 'min' => 8]),
                'password.confirmed' => __('validation.confirmed', ['attribute' => __('validation.attributes.password')]),
                'email.email'        => __('validation.email', ['attribute' => __('validation.attributes.email')]),
                'email.unique'       => __('validation.unique', ['attribute' => __('validation.attributes.email')]),
                'post_code.required' => __('validation.required', ['attribute' => __('validation.attributes.address')]),
                'address.required'   => __('validation.required', ['attribute' => __('validation.attributes.address')]),
                'phone.required'     => __('validation.required', ['attribute' => __('validation.attributes.phone')]),
                'first_name.required'=> __('validation.required', ['attribute' => __('validation.attributes.first_name')]),
                'referral_code.required'=> __('validation.required', ['attribute' => __('validation.attributes.referral_code')]),
                'referral_code_verified.required'=> __('validation.required', ['attribute' => __('validation.attributes.referral_code_verified')]),
            ];
        }
    }
