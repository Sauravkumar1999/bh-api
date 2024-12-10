<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AllowanceStatementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'payment_month' => $this->payment_month,
            'member_id' => $this->member_id,
            'commission' => $this->commission,
            'referral_bonus' => $this->referral_bonus,
            'headquarters_representative_allowance' => $this->headquarters_representative_allowance,
            'organization_division_allowance' => $this->organization_division_allowance,
            'policy_allowance' => $this->policy_allowance,
            'other_allowances' => $this->other_allowances,
            'income_tax' => $this->income_tax,
            'resident_tax' => $this->resident_tax,
            'year_end_settlement' => $this->year_end_settlement,
            'other_deductions_1' => $this->other_deductions_1,
            'other_deductions_2' => $this->other_deductions_2,
            'total_deduction' => $this->total_deduction,
            'total_before_tax' => $this->total_before_tax,
            'deducted_amount_received' => $this->deducted_amount_received,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
