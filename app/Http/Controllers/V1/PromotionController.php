<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PromotionResource;
use App\Traits\HelpersTraits;
use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Route;

class PromotionController extends Controller
{

    /**
     * @OA\Get(
     *      path="/api/v1/promotions",
     *      tags={"Promotions"},
     *      summary="Get Promotion List",
     *      description="Get Promotions List",
     *      @OA\Parameter(
     *          name="section",
     *          in="query",
     *          required=false,
     *          @OA\Schema(
     *              type="string",
     *          ),
     *          description="Filter based on section"
     *      ),
     *      @OA\Parameter(
     *          name="agent",
     *          in="query",
     *          required=false,
     *          @OA\Schema(
     *              type="string",
     *          ),
     *          description="Filter based on agent"
     *      ),
     *       @OA\Parameter(
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
     *      @OA\Response(
     *         response=404,
     *         description="Promotion not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Promotion not found"
     *             )
     *         )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $currentTimestamp = Carbon::now()->format('Y-m-d H:i:s');
        DB::enableQueryLog();

        $promotion = Promotion::whereJsonContains('section', $request->section)
        ->whereJsonContains('agent', $request->agent)
        ->where('start_at', '<=', $currentTimestamp)
        ->where('expired_at', '>=', $currentTimestamp)
        ->first();
        if (!$promotion) {
            return HelpersTraits::sendError(null, __('messages.promotion_not_found'));
        }

        $promotion = new PromotionResource($promotion);
        return HelpersTraits::sendResponse($promotion, __('messages.promotion_found'));
    }
}
