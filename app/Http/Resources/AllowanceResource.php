<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class AllowanceResource extends JsonResource
{
  public function toArray($request)
  {

    return [
      'id' => $this->id,
      'member_id' => $this->member_id,
      'code' => $this->member->code,
      'name' => $this->member->first_name .' '. $this->member->last_name,
      'position' => $this->member->roles->first()->name ?? null,
      'payment_month' => $this->payment_month,
      'commision' => $this->commision,
      'referral_bonus' => $this->referral_bonus,
      'total_before_tax' => $this->total_before_tax,
      'income_tax'  => $this->income_tax,
      'resident_tax' => $this->resident_tax,
      'year_end_settlement' => $this->year_end_settlement,
      'other_deductions_1' => $this->other_deductions_1,
      'other_deductions_2' => $this->other_deductions_2,
      'total_deduction'  => $this->total_deduction,
      'deducted_amount_received' => $this->deducted_amount_received,
      'first_registration_date' => $this->created_at,
      'last_modified_date'  => $this->updated_at,
      'extra_pay' => [
        'headquarters_representative' => $this->headquarters_representative_allowance,
        'organizational_division'     => $this->organization_division_allowance,
        'policy'                      => $this->policy_allowance,
        'other_allowance'             => $this->other_allowances
      ],
    ];
  }
}
