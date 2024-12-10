<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRequest;
use App\Http\Resources\BankResource;
use App\Models\Bank;
use App\Traits\HelpersTraits;

class BankController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/banks",
     *     tags={"Bank"},
     *     summary="Get Bank List",
     *     description="Get Bank List",
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=10
     *         ),
     *         description="Number of Bank per page"
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=1
     *         ),
     *         description="Number of page"
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
     *         description="Bank list found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="object")
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Bank list found"
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(
     *                     property="pagination",
     *                     type="object",
     *                     @OA\Property(
     *                         property="total",
     *                         type="integer",
     *                         example=100
     *                     ),
     *                     @OA\Property(
     *                         property="count",
     *                         type="integer",
     *                         example=10
     *                     ),
     *                     @OA\Property(
     *                         property="per_page",
     *                         type="integer",
     *                         example=10
     *                     ),
     *                     @OA\Property(
     *                         property="current_page",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="total_pages",
     *                         type="integer",
     *                         example=10
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid code",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Bank List not found"
     *             )
     *         )
     *     )
     * )
     */
    public function bankListing(FilterRequest $request)
    {
        try {
            $filters = $request->filters();
            $banks = Bank::orderBy('id', 'DESC')->filterAndPaginate($filters);
            if ($banks->isEmpty()) {
                return HelpersTraits::sendResponse(null, __('messages.not_found'), null);
            }
            $bank = BankResource::collection($banks);
            return HelpersTraits::sendResponse($bank, __('messages.found'), $bank);
        } catch (\Exception $e) {
            return HelpersTraits::sendError($e->getMessage());
        }
    }
}
