<?php

namespace App\Services;

use App\Http\Resources\AllowanceStatementResource;
use App\Traits\HelpersTraits;
use Illuminate\Support\Facades\Auth;

class AllowanceStatementService
{
    use HelpersTraits;

    protected $collection;

    public function __construct()
    {
        $this->collection = "allowance-statements";
    }

    public function getAllAllowanceStatements($filters)
    {
        try {
            $user = Auth::user();

            // Retrieve allowance statements for the authenticated non-admin user
            $allowanceStatements = $user->allowance;

            return $this->sendResponse(
                AllowanceStatementResource::collection($allowanceStatements), 
                __('messages.allowance_statements_retrieved')
            );
            
        } catch (\Exception $e) {
            return $this->sendError(
                __('messages.error_retrieving_allowance_statements'), 
                ['error' => $e->getMessage()]
            );
        }
    }

    public function showSingleAllowanceStatement($month)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->sendError(__('auth.user_not_found'), [], 401);
            }

            // Retrieve single allowance statement for the specified month
            $allowance = $user->allowance->first(function ($allowance) use ($month) {
                return strtoupper($allowance->payment_month) === strtoupper($month);
            });

            if (!$allowance) {
                return $this->sendError(__('messages.allowance_statement_not_found'));
            }

            return $this->sendResponse(
                new AllowanceStatementResource($allowance), 
                __('messages.allowance_statements_retrieved')
            );
            
        } catch (\Exception $e) {
            return $this->sendError(
                __('messages.error_retrieving_allowance_statement'), 
                ['error' => $e->getMessage()]
            );
        }
    }
}
