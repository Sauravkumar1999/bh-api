<?php

namespace App\Http\Requests;

use App\Rules\PhoneNumber;
use App\Rules\PasswordRequirement;
use App\Rules\NumberFormatRule;
use App\Traits\HelpersTraits;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\RequiredIf;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Schema(
 *     schema="ChannelRequest",
 *     type="object",
 *     required={"channel_name", "channel_price", "sale_rights", "approval_rights[]", "sr[]", "user_id", "channel_description", "sale_status", "contact_notifications", "bp", "ba", "md", "pmd", "commission_type", "h_pmdRadioOptions", "h_mdRadioOptions", "referral_bonusRadioOptions"},
 *     @OA\Property(
 *       property="channel_name",
 *       type="string",
 *       description="channel Name"
 *     ),
 *     @OA\Property(
 *       property="channel_description",
 *       type="string",
 *       description="channel Description"
 *     ),
 *     @OA\Property(
 *       property="channel_price",
 *       type="number",
 *       format="float",
 *       example=10.99,
 *       description="Price of the channel. Should be a numeric value with up to 2 decimal places."
 *     ),
 *      @OA\Property(
 *         property="commission_type",
 *         type="string",
 *         enum={"with-ratio", "fixed-price"},
 *         description="Commission Type"
 *     ),
 *     @OA\Property(
 *       property="main_url",
 *       type="string",
 *       format="url",
 *       example="https://swagger.io",
 *       description="Main URL"
 *     ),
 *     @OA\Property(
 *       property="url_1",
 *       type="string",
 *       format="url",
 *       example="https://swagger.io",
 *       description="URL 1"
 *     ),
 *     @OA\Property(
 *       property="sale_rights",
 *       type="string",
 *       enum={"full_disclosure", "partial_disclosure"},
 *       description="Sale Rights"
 *     ),
 *     @OA\Property(
 *       property="other_fees",
 *       type="number",
 *       format="float",
 *       example=10.99,
 *       description="Othere Fees"
 *     ),
 *     @OA\Property(
 *       property="user_id",
 *       type="integer",
 *       format="int64",
 *       example=10,
 *       minimum=1,
 *       description="User id; the person in charge"
 *     ),
 *     @OA\Property(
 *       property="url_params[]",
 *       type="array",
 *       @OA\Items(
 *             type="string",
 *             example="bhid={01033527689}"
 *       ),
 *       description="URL Parameters bhid={user code}"
 *     ),
 *     @OA\Property(
 *       property="approval_rights[]",
 *       type="array",
 *        @OA\Items(
 *             type="integer",
 *             format="int64",
 *             minimum=1,
 *             example="1"
 *         ),
 *       description="Users id with sale approving authority"
 *     ),
 *     @OA\Property(
 *       property="sr[]",
 *       type="array",
 *        @OA\Items(
 *             type="integer",
 *             format="int64",
 *             minimum=1,
 *             example="1"
 *         ),
 *       description="Users id with selling authority"
 *     ),
 *     @OA\Property(
 *       property="company_id",
 *       type="integer",
 *       format="int64",
 *       example=10,
 *       minimum=1,
 *       description="Company Id"
 *     ),
 *     @OA\Property(
 *       property="exposer_order",
 *       type="number",
 *       format="int64",
 *       example=10,
 *       description="the exposure order of a channel the less the number the more it appears on the top of the list"
 *     ),
 *     @OA\Property(
 *         property="bh_sale_commissions",
 *         type="number",
 *         format="float",
 *         example="10",
 *         description="BH Sale Commission. If commission_type is 'with-ratio', value can be a percentage (up to 100). If commission_type is 'fixed price', value can be any number."
 *     ),
 *     @OA\Property(
 *         property="sale_status",
 *         type="string",
 *         enum={"normal", "pause", "stop-selling", "onetime-sell"},
 *         description="Sale Status"
 *     ),
 *     @OA\Property(
 *         property="contact_notifications",
 *         type="integer",
 *         description="Flag indicating if the channel needs contact notification",
 *         enum={0, 1},
 *         example=1
 *     ),
 *      @OA\Property(
 *         property="referral_bonusRadioOptions",
 *         type="string",
 *         enum={"applied", "not_applied"},
 *         description="Referral bonus application will be applied or not"
 *     ),
 *     @OA\Property(
 *         property="referral_bonus",
 *         type="number",
 *         format="float",
 *         example=10.99,
 *         description="Referal Bonus (required if referral_bonusRadioOptions is 'applied'). If commission_type is 'with-ratio', value can be a percentage (up to 100). If commission_type is 'fixed price', value can be any number."
 *     ),
 *     @OA\Property(
 *         property="bp",
 *         type="number",
 *         format="float",
 *         example=10.99,
 *         description="Branch Representative(BP). If commission_type is 'with-ratio', value can be a percentage (up to 100). If commission_type is 'fixed price', value can be any number."
 *     ),
 *     @OA\Property(
 *         property="ba",
 *         type="number",
 *         format="float",
 *         example=10.99,
 *         description="Branch Representative(BA). If commission_type is 'with-ratio', value can be a percentage (up to 100). If commission_type is 'fixed price', value can be any number."
 *     ),
 *     @OA\Property(
 *         property="md",
 *         type="number",
 *         format="float",
 *         example=10.99,
 *         description="Headquarters Representative (MD). If commission_type is 'with-ratio', value can be a percentage (up to 100). If commission_type is 'fixed price', value can be any number."
 *     ),
 *     @OA\Property(
 *         property="pmd",
 *         type="number",
 *         format="float",
 *         example=10.99,
 *         description="Headquarters Representative (PMD). If commission_type is 'with-ratio', value can be a percentage (up to 100). If commission_type is 'fixed price', value can be any number."
 *     ),
 *      @OA\Property(
 *         property="h_mdRadioOptions",
 *         type="string",
 *         enum={"applied", "not_applied"},
 *         description="Option for Headquarters representative allowance (HMD) will be applied or not"
 *     ),
 *     @OA\Property(
 *         property="h_md",
 *         type="number",
 *         format="float",
 *         example=10.99,
 *         description="Headquarters representative allowance (HMD) (required if h_mdRadioOptions is 'applied'). If commission_type is 'with-ratio', value can be a percentage (up to 100). If commission_type is 'fixed price', value can be any number."
 *     ),
 *      @OA\Property(
 *         property="h_pmdRadioOptions",
 *         type="string",
 *         enum={"applied", "not_applied"},
 *         description="Headquarters representative allowance (HPMD) will be applied or not"
 *     ),
 *     @OA\Property(
 *         property="h_pmd",
 *         type="number",
 *         format="float",
 *         example=10.99,
 *         description="Headquarters representative allowance (HPMD) (required if h_pmdRadioOptions is 'applied'). If commission_type is 'with-ratio', value can be a percentage (up to 100). If commission_type is 'fixed price', value can be any number."
 *     ),
 *     @OA\Property(
 *         property="banner",
 *         type="string",
 *         format="binary",
 *         description="Banner photo file. Accepts only jpeg, png, jpg, gif"
 *     ),
 * )
 */

class ChannelRequest extends FormRequest
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
        $channelPriceRule = null;

        if($this->routeIs('channels.update')) $channelPriceRule = [new RequiredIf($this->commission_type == 'fixed-price')];

        return [
            "channel_name"          => "required|string",
            "user_id"               => 'required|exists:users,id',
            "sale_rights"           => "required",
            "approval_rights"       => "required|array",
            "approval_rights.*"     => "required|exists:users,id",
            "sr"       => "required|array",
            "sr.*"     => "required|exists:users,id",
            'commission_type'       => 'required|in:fixed-price,with-ratio',
            "channel_description"   => "required",
            "other_fees"            => 'required|between:0,9999999.99|regex:/^[\d\.,]+$/',
            // "bh_operating_profit"   => 'required|between:0,9999999.99',
            "channel_price"         => $channelPriceRule ?? 'required|between:0,9999999.99',
            "bp"                    => ['required', 'between:0,9999999.99', new NumberFormatRule($this->getFormatPattern())],
            "ba"                    => ['required', 'between:0,9999999.99', new NumberFormatRule($this->getFormatPattern())],
            "md"                    => ['required', 'between:0,9999999.99', new NumberFormatRule($this->getFormatPattern())],
            "pmd"                   => ['required', 'between:0,9999999.99', new NumberFormatRule($this->getFormatPattern())],
            'h_mdRadioOptions'      => 'required',
            "h_md"                  => [new RequiredIf($this->h_mdRadioOptions == 'applied'), 'between:0,9999999.99', new NumberFormatRule($this->getFormatPattern())],
            'h_pmdRadioOptions'     => 'required',
            "h_pmd"                 => [new RequiredIf($this->h_pmdRadioOptions == 'applied'), 'between:0,9999999.99', new NumberFormatRule($this->getFormatPattern())],
            'referral_bonusRadioOptions' => 'required',
            "referral_bonus"        => [new RequiredIf($this->referral_bonusRadioOptions == 'applied'), 'between:0,9999999.99', 'regex:/^[\d\.,]+$/'],
            'bh_sale_commissions'   => 'nullable | between:0,9999999.99',
            "main_url"              => "nullable|url",
            'url_params'            => 'nullable | array',
            "url_1"                 => "nullable|url",
            "company_id"            => [new RequiredIf($this->sale_rights != 'full_disclosure'), 'exists:companies,id'],
            "sale_status"           => "required",
            "contact_notifications" => "required",
            "exposer_order"         => "nullable|numeric",
            "banner"                   => "nullable|image|mimes:jpeg,jpg,png,gif",
        ];
    }

    public function messages()
    {
        return [
            'channel_name.required'                 => __('validation.required', ['attribute' => __('validation.attributes.channel_name')]),
            'user_id.required'                      => __('validation.required', ['attribute' => __('validation.attributes.user_id')]),
            'user_id.exists'                      => __('validation.exists', ['attribute' => __('validation.attributes.user_id')]),
            'sale_rights.required'                  => __('validation.required', ['attribute' => __('validation.attributes.sale_rights')]),
            'approval_rights.required'              => __('validation.required', ['attribute' => __('validation.attributes.approval_rights')]),
            'approval_rights.array'                 => __('validation.array', ['attribute' => __('validation.attributes.approval_rights')]),
            'approval_rights.*'                     => __('validation.exists', ['attribute' => __('validation.attributes.approval_rights')]),
            'sr.required'                           => __('validation.required', ['attribute' => __('validation.attributes.sr')]),
            'sr.array'                              => __('validation.array', ['attribute' => __('validation.attributes.sr')]),
            'sr.*'                                  => __('validation.exists', ['attribute' => __('validation.attributes.sr')]),
            'commission_type.required'              => __('validation.required', ['attribute' => __('validation.attributes.commission_type')]),
            'channel_description.required'                 => __('validation.required', ['attribute' => __('validation.attributes.channel_description')]),
            'other_fees.required'                   => __('validation.required', ['attribute' => __('validation.attributes.other_fees')]),
            'other_fees.between'                   => __('validation.between', ['attribute' => __('validation.attributes.other_fees')]),
            'channel_price.required'                => __('validation.required', ['attribute' => __('validation.attributes.channel_price')]),
            'channel_price.between'                => __('validation.between', ['attribute' => __('validation.attributes.channel_price')]),
            'bp.required'                           => __('validation.required', ['attribute' => __('validation.attributes.bp')]),
            'bp.between'                           => __('validation.between', ['attribute' => __('validation.attributes.bp')]),
            'ba.required'                           => __('validation.required', ['attribute' => __('validation.attributes.ba')]),
            'ba.between'                           => __('validation.between', ['attribute' => __('validation.attributes.ba')]),
            'md.required'                           => __('validation.required', ['attribute' => __('validation.attributes.md')]),
            'md.between'                           => __('validation.between', ['attribute' => __('validation.attributes.md')]),
            'pmd.required'                          => __('validation.required', ['attribute' => __('validation.attributes.pmd')]),
            'pmd.between'                          => __('validation.between', ['attribute' => __('validation.attributes.pmd')]),
            'h_md.required'                         => __('validation.required', ['attribute' => __('validation.attributes.h_md')]),
            'h_md.between'                         => __('validation.between', ['attribute' => __('validation.attributes.h_md')]),
            'h_pmd.required'                        => __('validation.required', ['attribute' => __('validation.attributes.h_pmd')]),
            'h_pmd.between'                        => __('validation.between', ['attribute' => __('validation.attributes.h_pmd')]),
            'h_pmdRadioOptions.required'            => __('validation.required', ['attribute' => __('validation.attributes.h_pmdRadioOptions')]),
            'h_mdRadioOptions.required'             => __('validation.required', ['attribute' => __('validation.attributes.h_mdRadioOptions')]),
            'referral_bonus.required'               => __('validation.required', ['attribute' => __('validation.attributes.referral_bonus')]),
            'referral_bonus.between'               => __('validation.between', ['attribute' => __('validation.attributes.referral_bonus')]),
            'referral_bonusRadioOptions.required'   => __('validation.required', ['attribute' => __('validation.attributes.referral_bonusRadioOptions')]),
            'bh_sale_commissions.between'                              => __('validation.between', ['attribute' => __('validation.attributes.bh_sale_commissions')]),
            'main_url.url'                              => __('validation.url', ['attribute' => __('validation.attributes.main_url')]),
            'url_params.array'                              => __('validation.array', ['attribute' => __('validation.attributes.url_params')]),
            'url_1.url'                              => __('validation.url', ['attribute' => __('validation.attributes.url_1')]),
            'company_id.required'                   => __('validation.required', ['attribute' => __('validation.attributes.company_id')]),
            'sale_status.required'                => __('validation.required', ['attribute' => __('validation.attributes.sale_status')]),
            'contact_notifications.required'                => __('validation.required', ['attribute' => __('validation.attributes.contact_notifications')]),
            'exposer_order.numeric'                            =>__('validation.numeric', ['attribute' => __('validation.attributes.exposer_order')]),
            'banner.images'                         => __('validation.images', ['attribute' => __('validation.attributes.banner')]),
        ];
    }


    /**
     * Get the price format pattern based on the commission type
     *
     * @return string
     */
    private function getFormatPattern()
    {
        return $this->commission_type == 'with-ratio' ? '/^[0-9]+(\.[0-9]{1,2})?$/' : '/^\d{1,3}(,\d{3})*$/';
    }
}
