<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="ManageHomepageRequest",
 *     type="object",
 *     required={"contact_number", "contact_email"},
 *     @OA\Property(property="bio_text", type="string", description="Bio Text"),
 *     @OA\Property(property="is_bio_enable", type="integer", example=1, enum={0, 1}, description="0 for false, 1 for true"),
 *     @OA\Property(property="sales_person_image", type="string", format="binary", description="Sales person photo file. Accepts only jpeg, png, jpg"),
 *     @OA\Property(property="is_sale_person_image_enable", type="integer", example=1, enum={0, 1}, description="0 for false, 1 for true"),
 *     @OA\Property(property="contact_number", type="string", description="Conact Number", example="82105553820"),
 *     @OA\Property(property="contact_email", type="string", format="email", description="Contact Email"),
 *     @OA\Property(property="portfolio", type="string", description="Portfolio description"),
 *     @OA\Property(property="facebook_url", type="string", format="url", example="https://www.facebook.com", description="Facebook Url"),
 *     @OA\Property(property="fa_status", type="integer", example=1, enum={0, 1}, description="Facebook: 0 for false, 1 for true"),
 *     @OA\Property(property="instagram_url", type="string", format="url", example="https://www.instagram.com", description="Insagram Url"),
 *     @OA\Property(property="in_status", type="integer", example=1, enum={0, 1}, description="Insagram: 0 for false, 1 for true"),
 *     @OA\Property(property="kakaotalk_url", type="string", format="url", example="https://www.kakaotalk.com", description="KakaoTalk Url"),
 *     @OA\Property(property="ko_status", type="integer", example=1, enum={0, 1}, description="KakaoTalk: 0 for false, 1 for true"),
 *     @OA\Property(property="blog_url", type="string", format="url", example="https://www.blog.com", description="Blog Url"),
 *     @OA\Property(property="bl_status", type="integer", example=1, enum={0, 1}, description="Blog: 0 for false, 1 for true")
 * )
 */

class ManageHomepageRequest extends FormRequest
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
            'bio_text' => "required_if:name_status, '=', 1",
            'is_bio_enable' => 'required|boolean',
            'sales_person_image' => 'required|image|mimes:png,jpg,jpeg',
            'is_sale_person_image_enable' => 'required|boolean',
            'contact_number'    => 'required',
            'contact_email'     => 'required',
            'portfolio'         => 'nullable',
            'facebook_url'      => "required_if:fa_status, '=', 1",
            'fa_status' => 'required|boolean',
            'instagram_url'     => "required_if:in_status, '=', 1",
            'in_status' => 'required|boolean',
            'kakaotalk_url'     => "required_if:ko_status, '=', 1",
            'ko_status' => 'required|boolean',
            'blog_url'          => "required_if:bl_status, '=', 1",
            'bl_status' => 'required|boolean'
        ];
    }

    public function messages()
    {
        return [
            'is_sale_person_image_enable.required'                    => __('validation.required', ['attribute' => __('validation.attributes.is_sale_person_image_enable')]),
            'is_sale_person_image_enable.boolean'                    => __('validation.required', ['attribute' => __('validation.attributes.is_sale_person_image_enable')]),
            'bio_text.required'                 => __('validation.required', ['attribute' => __('validation.attributes.bio_text')]),
            'contact_number.required'           => __('validation.required', ['attribute' => __('validation.attributes.contact_number')]),
            'contact_email.required'            => __('validation.required', ['attribute' => __('validation.attributes.contact_email')]),
            'facebook_url.required'             => __('validation.required', ['attribute' => __('validation.attributes.facebook_url')]),
            'instagram_url.required'            => __('validation.required', ['attribute' => __('validation.attributes.instagram_url')]),
            'kakaotalk_url.required'            => __('validation.required', ['attribute' => __('validation.attributes.kakaotalk_url')]),
            'blog_url.required'                 => __('validation.required', ['attribute' => __('validation.attributes.blog_url')]),
            'bl_status.required'                => __('validation.required', ['attribute' => __('validation.attributes.bl_status')]),
            'bl_status.boolean'                => __('validation.required', ['attribute' => __('validation.attributes.bl_status')]),
            'is_bio_enable.required'              => __('validation.required', ['attribute' => __('validation.attributes.is_bio_enable')]),
            'is_bio_enable.boolean'              => __('validation.required', ['attribute' => __('validation.attributes.is_bio_enable')]),
            'fa_status.required'                => __('validation.required', ['attribute' => __('validation.attributes.fa_status')]),
            'fa_status.boolean'                => __('validation.required', ['attribute' => __('validation.attributes.fa_status')]),
            'in_status.required'                => __('validation.required', ['attribute' => __('validation.attributes.in_status')]),
            'in_status.boolean'                => __('validation.required', ['attribute' => __('validation.attributes.in_status')]),
            'ko_status.required'                => __('validation.required', ['attribute' => __('validation.attributes.ko_status')]),
            'ko_status.boolean'                => __('validation.required', ['attribute' => __('validation.attributes.ko_status')]),
        ];
    }
}
