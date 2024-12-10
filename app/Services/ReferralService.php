<?php

namespace App\Services;

use App\Traits\HelpersTraits;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Resources\ReferralResource;
use App\Http\Resources\UserResource;


class ReferralService
{
    use HelpersTraits;

    public function getReferrals($filters)
    {
        try {
            $loggedInUser = Auth::user();
    
            if (is_admin_user()) {
                // If user is admin, fetch all referrals
                $referral = User::withDepth()->hasChildren()->filterAndPaginate($filters);
                $referrals = UserResource::collection($referral);
                return HelpersTraits::sendResponse($referral,__('messages.referrals_fetched'),$referrals);
            } else {
                // If user is not admin, fetch only their referrals
                return HelpersTraits::sendResponse(UserResource::collection($loggedInUser->descendants()->withDepth()->filterAndPaginate($filters)), __('messages.referrals_fetched'));
            }
        } catch (\Exception $e) {
            return HelpersTraits::sendError(__('messages.error_fetching_referrals'), $e->getMessage());
        }
    }
    

    public function getReferralTree($id)
    {
        $user = $id ? User::findOrFail($id) : Auth::user();
        return $this->buildReferralTree($user);
    }

    private function buildReferralTree(User $user)
    {

        try {
            $children = $user->children()->with('roles', 'company', 'children')->get();
            $user->children = $children->map(function ($child) {
                return $this->buildReferralTree($child);
            });
            $referralData = ReferralResource::collection([$user]);
            return HelpersTraits::sendResponse($referralData, __('messages.referral_tree_fetched'));
        } catch (\Exception $e) {
            return HelpersTraits::sendError(__('messages.error_fetching_referral_tree'), $e->getMessage());
        }
    }
}


