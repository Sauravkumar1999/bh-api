<?php

namespace App\Services;

use Exception;
use App\Models\Allowance;
use App\Http\Resources\AllowanceResource;

class AllowanceService
{
    private $validateRequestData;
    private $allowance;

    public function createAllowance($request)
    {
        try{
            $this->validateRequestData = $request->validated();
            $this->allowance = Allowance::create([
                'payment_month' => $this->validateRequestData['payment_month'],
                'member_id' => $this->validateRequestData['member_id'],
                'commission' => $this->validateRequestData['commission'],
                'referral_bonus' => $this->validateRequestData['referral_bonus'],
                'headquarters_representative_allowance' => $this->validateRequestData['headquarters_representative_allowance'],
                'organization_division_allowance' => $this->validateRequestData['organization_division_allowance'],
                'other_allowances' => $this->validateRequestData['other_allowances'],
                'income_tax' => $this->validateRequestData['income_tax'],
                'resident_tax' => $this->validateRequestData['resident_tax'],
                'year_end_settlement' => $this->validateRequestData['year_end_settlement'],
                'other_deductions_1' => $this->validateRequestData['other_deductions_1'],
                'other_deductions_2' => $this->validateRequestData['other_deductions_2'],
                'total_deduction' => $this->validateRequestData['other_deductions_1'],
                'total_before_tax' => $this->validateRequestData['other_deductions_2'],
                'deducted_amount_received' => $this->validateRequestData['deducted_amount_received'],
            ]);
            $allowance = new AllowanceResource($this->allowance);
            return [
                'success' => true,
                'message' => "messages.allowance_success",
				'data' => $allowance,
              ];
        }catch(Exception $e){
            return [
                'success' => false,
                'message' => $e->getMessage()
              ];
        }
    }

	public function updateAllowance($request, $id)
	{
		try{
			$this->validateRequestData = $request->validated();
			$allowance = Allowance::findOrFail($id);

			$allowance->payment_month = $this->validateRequestData['payment_month'];
			$allowance->member_id = $this->validateRequestData['member_id'];
			$allowance->commission = $this->validateRequestData['commission'];
			$allowance->referral_bonus = $this->validateRequestData['referral_bonus'];
			$allowance->headquarters_representative_allowance = $this->validateRequestData['headquarters_representative_allowance'];
			$allowance->organization_division_allowance = $this->validateRequestData['organization_division_allowance'];
			$allowance->other_allowances = $this->validateRequestData['other_allowances'];
			$allowance->income_tax = $this->validateRequestData['income_tax'];
			$allowance->resident_tax = $this->validateRequestData['resident_tax'];
			$allowance->year_end_settlement = $this->validateRequestData['year_end_settlement'];
			$allowance->other_deductions_1 = $this->validateRequestData['other_deductions_1'];
			$allowance->other_deductions_2 = $this->validateRequestData['other_deductions_2'];
			$allowance->total_deduction = $this->validateRequestData['other_deductions_1'];
			$allowance->total_before_tax = $this->validateRequestData['other_deductions_2'];
			$allowance->deducted_amount_received = $this->validateRequestData['deducted_amount_received'];
			$allowance->save();

            $allowance = new AllowanceResource($allowance);
            return [
                'success' => true,
                'message' => "messages.allowance_updated_success",
				'data' => $allowance,
              ];
		}catch(Exception $e){
			return [
				'success' => false,
				'message' => $e->getMessage()
			];
		}
	}

    public function viewAllowance($id)
    {
        try{
            $allowance = Allowance::findOrFail($id);
            $allowance = new AllowanceResource($allowance);
            return [
                'success' => true,
                'message' => "messages.allowance_updated_success",
				'data' => $allowance,
              ];
        }catch(Exception $e){
			return [
				'success' => false,
				'message' => $e->getMessage()
			];
        }
    }

	public function deleteAllowance($id)
	{
		try {
			$allowance = Allowance::findOrFail($id);
			$allowance->delete();
            return [
                'success' => true,
                'message' => "messages.allowance_deleted_success",
              ];
		}catch(Exception $e){
			return [
				'success' => false,
				'message' => $e->getMessage()
			];
		}
	}

}
