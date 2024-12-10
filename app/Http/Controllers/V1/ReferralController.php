<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\ReferralService;
use App\Http\Requests\FilterRequest;
use Illuminate\Support\Facades\Auth;


class ReferralController extends Controller
{
    protected $service;


    public function __construct(ReferralService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/referrals",
     *     tags={"Referrals"},
     *     summary="Fetch all referrals",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         description="Referrals page"
     *     ),
     *     @OA\Parameter(
     *          name="Accept-Language",
     *          in="header",
     *          description="Language code for language selection",
     *          required=true,
     *          @OA\Schema(
     *              type="string",
     *              enum=LOCALE_ENUM,
     *              default="ko",
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Referrals fetched successfully",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error fetching referrals"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index(FilterRequest $request)
    {

            $filters = $request->filters();
            return $this->service->getReferrals($filters);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/referrals/{id}/view",
     *     tags={"Referrals"},
     *     summary="Fetch referral tree",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         description="Referral ID"
     *     ),
     *     @OA\Parameter(
     *          name="Accept-Language",
     *          in="header",
     *          description="Language code for language selection",
     *          required=true,
     *          @OA\Schema(
     *              type="string",
     *              enum=LOCALE_ENUM,
     *              default="ko",
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Referral tree fetched successfully",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error fetching referral tree"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function referralTree($id = null)
    {

            // Check if the authenticated user is an admin
            $loggedInUser = Auth::user();
            if (!is_admin_user()) {
                // If not an admin, override the ID with the authenticated user's ID
                $id = $loggedInUser->id;
            }
            return $this->service->getReferralTree($id);
    }
}


