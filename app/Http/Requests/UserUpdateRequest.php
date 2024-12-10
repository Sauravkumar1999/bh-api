<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\Contact;

/**
 * @OA\Schema(
 *     schema="UserUpdateRequest",
 *     @OA\Property(property="status", type="integer", example=1, enum={0, 1}, description="0 for false, 1 for true"),
 *     @OA\Property(property="user_type", type="string", example="admin"),
 *     @OA\Property(property="company_id", type="integer", example=1),
 *     @OA\Property(property="role_id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="contact", type="string", example="+1234567890"),
 *     @OA\Property(property="dob", type="string", format="date", example="1990-01-01"),
 *     @OA\Property(property="gender", type="string", enum={"male", "female"}, example="male"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="password", type="string", format="password", example="password123"),
 *     @OA\Property(property="address", type="string", example="123 Main St"),
 *     @OA\Property(property="recommender", type="string", example="ref_code_123")
 * )
 */
class UserUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function all($keys = null)
    {
        $data = parent::all($keys);

        $data['user_id'] = $this->route('user_id');

        return $data;
    }

    public function rules()
    {
        $userId = $this->input('user_id');
        return [
            'user_id' => 'required|exists:users,id',
            'status' => 'nullable|boolean',
            'user_type' => 'required|string',
            'company_id' => 'nullable|exists:companies,id',
            'role_id' => 'nullable|exists:roles,id',
            'name' => 'required|string',
            'contact' => [
                'required',
                Rule::unique('contacts', 'telephone_1')->ignore($userId),
            ],
            'dob' => 'required|date',
            'gender' => 'nullable|string|in:male,female',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => 'nullable|min:8',
            'address' => 'nullable|string',
            'recommender' => 'required|exists:users,code',
        ];
    }

    public function messages()
    {
        return [
            'status.boolean'    => __('validation.boolean', ['attribute' => __('validation.attributes.status')]),
            'user_type.string'  => __('validation.string', ['attribute' => __('validation.attributes.user_type')]),
            'company_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.company_id')]),
            'role_id.exists'    => __('validation.exists', ['attribute' => __('validation.attributes.role_id')]),
            'name.string'       => __('validation.string', ['attribute' => __('validation.attributes.name')]),
            'contact.unique'    => __('validation.unique', ['attribute' => __('validation.attributes.contact')]),
            'dob.date_format'   => __('validation.date_format', ['attribute' => __('validation.attributes.dob')]),
            'gender.unique'     => __('validation.unique', ['attribute' => __('validation.attributes.contact')]),
            'email.email'       => __('validation.email', ['attribute' => __('validation.attributes.email')]),
            'password.min'      => __('validation.min', ['attribute' => __('validation.attributes.password')]),
            'address.string'    => __('validation.string', ['attribute' => __('validation.attributes.address')]),
            'recommender.exists' => __('validation.exists', ['attribute' => __('validation.attributes.recommender')]),
        ];
    }
}
