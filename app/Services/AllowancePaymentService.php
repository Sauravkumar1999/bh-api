<?php

namespace App\Services;

use App\Traits\HelpersTraits;
use App\Http\Requests\AllowancePaymentRequest;
use App\Http\Resources\AllowancePaymentResource;
use App\Models\AllowancePayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Plank\Mediable\Media;
use App\Services\AttachmentService;

class AllowancePaymentService
{
    use HelpersTraits;

    protected $attachmentService;
    protected $collection;

    public function __construct()
    {
        $this->attachmentService = new AttachmentService('uploadAllowancePayment');
        $this->collection = "allowance-payment";
    }

    public function getAllAllowancePayments($filters)
    {
        try {
            $user = Auth::user();

            if (!is_admin_user()) {
                return $this->sendError(__('auth.user_not_found_admin_required'));
            }            

            $allowancePayments = AllowancePaymentResource::collection(AllowancePayment::orderBy('id', 'DESC')->filterAndPaginate($filters));
            return $this->sendResponse($allowancePayments, __('messages.allowance_payments_retrieved'), $allowancePayments);
        } catch (\Exception $e) {
            return $this->sendError(__('messages.error_retrieving_allowance_payments'), ['error' => $e->getMessage()]);
        }
    }

    public function createAllowancePayment(AllowancePaymentRequest $request)
    {
        DB::beginTransaction();
        try {
            if (is_admin_user()) {
                // Validate and create new AllowancePayment
                $validatedData = $request->validated();
                $data = array_merge($validatedData, ['user_id' => Auth::id()]);
                $allowancePayment = AllowancePayment::create($data);

                // Attach media if provided
                if(isset($validatedData['attachment'])){
                    $allowancePayment['media'] = $this->attachmentService->attachMedia($allowancePayment, $validatedData, $this->collection);
                }

                DB::commit();

                return $this->sendResponse(AllowancePaymentResource::collection([$allowancePayment]), __('messages.allowance_payment_created'));

            }
        } catch (\Exception $e) {
            DB::rollBack();
            // Handle exceptions and return error response
            return $this->sendError(__('messages.error_creating_allowance_payment'), ['error' => $e->getMessage()]);
        }
    }

    public function updateAllowancePayment(AllowancePaymentRequest $request, int $id)
    {
        DB::beginTransaction();
        try {
            if (is_admin_user()) {
                // Validate and update existing AllowancePayment
                $validatedData = $request->validated();
                $data = array_merge($validatedData, ['user_id' => Auth::id()]);
                $allowancePayment = AllowancePayment::findOrFail($id);
                $allowancePayment->update($data);

                // Attach media if provided
                if(isset($validatedData['attachment'])){
                    $syncMedia = $this->attachmentService->syncAttchment($allowancePayment, $validatedData, $this->collection);
                    if($syncMedia){
                        $allowancePayment['media'] = $syncMedia;
                    }
                }

                DB::commit();

                return $this->sendResponse(AllowancePaymentResource::collection([$allowancePayment]), __('messages.allowance_payment_updated'));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            // Handle exceptions and return error response
            return $this->sendError(__('messages.error_updating_allowance_payment'), ['error' => $e->getMessage()]);
        }
    }

    public function deleteAllowancePayment(int $id)
    {
        DB::beginTransaction();
        try {
            if (is_admin_user()) {
                // Find and delete existing AllowancePayment
                $allowancePayment = AllowancePayment::findOrFail($id);
                
                // Delete the AllowancePayment
                $allowancePayment->delete();

                DB::commit();

                return $this->sendResponse(null, __('messages.allowance_payment_deleted'));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            // Handle exceptions and return error response
            return $this->sendError(__('messages.error_deleting_allowance_payment'), ['error' => $e->getMessage()]);
        }
    }

    public function showSingleAllowancePayment(int $id)
    {
        try {
            $allowancePayment = AllowancePayment::findOrFail($id);
            return $this->sendResponse(new AllowancePaymentResource($allowancePayment), __('messages.allowance_payment_retrieved'));
        } catch (\Exception $e) {
            return $this->sendError(__('messages.error_retrieving_allowance_payment'), ['error' => $e->getMessage()]);
        }
    }

}

