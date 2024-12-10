<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="RoleRequest",
 *     type="object",
 *     required={"name", "display_name", "description", "order"},
 *     @OA\Property(property="name", type="string", example="Tested Role"),
 *     @OA\Property(property="display_name", type="string", example="Displaying Tested Name"),
 *     @OA\Property(property="description", type="string", example="Lorem Ipsum Dolor Sit Amet"),
 *     @OA\Property(property="order", type="integer", example=15)
 * )
 */
class RoleRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return $this->createRules();
            case 'PUT':
            case 'PATCH':
                return $this->updateRules();
            case 'DELETE':
                return $this->deleteRules();
            default:
                return [];
        }
    }

    /**
     * Get the validation rules for creating a new role.
     *
     * @return array
     */
    private function createRules()
    {
        return [
            'name' => 'required|string|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'order' => 'required|integer',
        ];
    }

    /**
     * Get the validation rules for updating an existing role.
     *
     * @return array
     */
    private function updateRules()
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('roles', 'name')->ignore($this->route('role'))
            ],
            'display_name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'order' => 'required|integer',
        ];
    }

    /**
     * Get the validation rules for deleting an existing role.
     *
     * @return array
     */
    private function deleteRules()
    {
        return [];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => __('validation.required', ['attribute' => __('validation.attributes.name')]),
            'name.unique' => __('validation.unique', ['attribute' => __('validation.attributes.name')]),
            'display_name.required' => __('validation.required', ['attribute' => __('validation.attributes.display_name')]),
            'description.required' => __('validation.required', ['attribute' => __('validation.attributes.description')]),
            'order.required' => __('validation.required', ['attribute' => __('validation.attributes.order')]),
        ];
    }
}
