<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NumberFormatRule;

class AllowanceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'payment_month'                         => 'required|string',
            'member_id'                             => 'required|exists:users,id',
            'commission'                            => ['nullable', new NumberFormatRule],
            'referral_bonus'                        => ['nullable', new NumberFormatRule],
            'headquarters_representative_allowance' => ['required', new NumberFormatRule],
            'organization_division_allowance'       => ['required', new NumberFormatRule],
            'policy_allowance'                      => ['required', new NumberFormatRule],
            'other_allowances'                      => ['required', new NumberFormatRule],
            'income_tax'                            => ['required', new NumberFormatRule],
            'resident_tax'                          => ['required', new NumberFormatRule],
            'year_end_settlement'                   => ['required', new NumberFormatRule],
            'other_deductions_1'                    => ['required', new NumberFormatRule],
            'other_deductions_2'                    => ['required', new NumberFormatRule],
            'total_deduction'                       => ['required', new NumberFormatRule],
            'total_before_tax'                      => ['required', new NumberFormatRule],
            'deducted_amount_received'              => ['required', new NumberFormatRule],
        ];
    }

    public function messages()
    {
        return [
            'payment_month.required' => __('validation.required',['attribute' => __('validation.attributes.payment_month')]),
            'member_id.required' => __('validation.required',['attribute' => __('validation.attributes.member_id')]),
            'commission.required' => __('validation.required',['attribute' => __('validation.attributes.commission')]),
            'referral_bonus.required' => __('validation.required',['attribute' => __('validation.attributes.referral_bonus')]),
            'headquarters_representative_allowance.required' => __('validation.required',['attribute' => __('validation.attributes.headquarters_representative_allowance')]),
            'organization_division_allowance.required' => __('validation.required',['attribute' => __('validation.attributes.organization_division_allowance')]),
            'policy_allowance.required' => __('validation.required',['attribute' => __('validation.attributes.policy_allowance')]),
            'other_allowances.required' => __('validation.required',['attribute' => __('validation.attributes.other_allowances')]),
            'income_tax.required' => __('validation.required',['attribute' => __('validation.attributes.income_tax')]),
            'resident_tax.required' => __('validation.required',['attribute' => __('validation.attributes.resident_tax')]),
            'year_end_settlement.required' => __('validation.required',['attribute' => __('validation.attributes.year_end_settlement')]),
            'other_deductions_1.required' => __('validation.required',['attribute' => __('validation.attributes.other_deductions_1')]),
            'other_deductions_2.required' => __('validation.required',['attribute' => __('validation.attributes.other_deductions_2')]),
            'total_deduction.required' => __('validation.required',['attribute' => __('validation.attributes.total_deduction')]),
            'total_before_tax.required' => __('validation.required',['attribute' => __('validation.attributes.total_before_tax')]),
            'deducted_amount_received.required' => __('validation.required',['attribute' => __('validation.attributes.deducted_amount_received')]),
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
