<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Schema(
 *     schema="AllowanceRequest",
 *     type="object",
 *     required={"title", "detail"},
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="detail", type="string"),
 *     @OA\Property(property="attachment", type="string", nullable=true)
 * )
 */
class AllowancePaymentRequest extends FormRequest
{

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
     * Get the validation rules for creating a new allowance payment.
     *
     * @return array
     */
    private function createRules()
    {
        return [
            'title'      => 'required|string|max:255',
            'detail'     => 'required|string',
            'attachment' => 'nullable|file',
            'user_id'    => 'nullable|exists:users,id',
        ];
    }

    /**
     * Get the validation rules for updating an existing allowance payment.
     *
     * @return array
     */
    private function updateRules()
    {
        return [
            'title'      => 'required|string|max:255',
            'detail'     => 'required|string',
            'attachment' => 'nullable|file',
            'existing_attachment'  => 'nullable|string|exists:media,filename',
            'user_id'    => 'nullable|exists:users,id',
        ];
    }

    /**
     * Get the validation rules for deleting an existing allowance payment.
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
            'title.required'      => __('validation.required', ['attribute' => __('validation.attributes.title')]),
            'detail.required'     => __('validation.required', ['attribute' => __('validation.attributes.detail')]),
            'attachment.file'     => __('validation.file', ['attribute' => __('validation.attributes.attachment')]),
            'user_id.exists'      => __('validation.exists', ['attribute' => __('validation.attributes.user_id')]),
        ];
    }
}