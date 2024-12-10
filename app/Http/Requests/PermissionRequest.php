<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="PermissionRequest",
 *     type="object",
 *     required={"name", "display_name", "ltpm"},
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="display_name", type="string"),
 *     @OA\Property(property="ltpm", type="string"),
 * )
 */
class PermissionRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $nameRule = null;

        if ($this->routeIs('permissions.create'))
            $nameRule = 'required|string|unique:permissions,name';
        else if ($this->routeIs('permissions.update'))
            $nameRule = 'required|string|' . Rule::unique('permissions')->ignore($this->route('permission_id'));

        return [
            'name'              => $nameRule,
            'display_name'      => 'required|string',
            'description'       => 'nullable',
            'ltpm'              => 'required'

        ];
    }

    public function messages()
    {
        return [
            'name.required'  => __('validation.required', ['attribute' => __('validation.attributes.name')]),
            'display_name.required' => __('validation.required', ['attribute' => __('validation.attributes.display_name')]),
            'ltpm.required'     => __('validation.required', ['attribute' => __('validation.attributes.ltpm')]),
        ];
    }
}
