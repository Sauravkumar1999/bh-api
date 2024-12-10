<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="UserSettingUpdateRequest",
 *     type="object",
 *     @OA\Property(
 *         property="channels",
 *         type="array",
 *         @OA\Items(type="integer")
 *     ),
 *     @OA\Property(
 *         property="url_{chid}",
 *         type="string",
 *         format="url",
 *         description="URL for the channel",
 *         example="https://example.com"
 *     ),
 *     @OA\Property(
 *         property="channel_expose_{chid}",
 *         type="string",
 *         description="Exposure status for the product, accepted values: no, off",
 *         example="no"
 *     ),
 * )
 */
class UserSettingUpdateRequest extends FormRequest
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
            'channels'=>'array|exists:channels,id'
        ];

        if ($this->has('channels')) {
            foreach ($this->input('channels') as $chid) {
                $rules['url_' . $chid] = 'nullable|url';
                $rules['channel_expose_' . $chid] = 'required|in:no,off';
            }
        }

        return $rules;
    }

    /**
     * Customize the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        $messages = [];

        if ($this->has('channels')) {
            foreach ($this->input('channels') as $chid) {
                $messages['channel_expose_' . $chid . '.in'] = __('validation.channel_expose', ['attribute' => 'channel_expose_' . $chid]);
            }
        }

        return $messages;
    }
}
