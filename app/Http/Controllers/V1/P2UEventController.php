<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRequest;
use App\Http\Resources\P2UEventResource;
use App\Http\Resources\p2uEventSumResource;
use App\Models\P2UEvent;
use App\Services\P2UEventService;
use Illuminate\Http\Request;
use App\Traits\HelpersTraits;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class P2UEventController extends Controller
{


    protected $p2uEventService;

    public function __construct(P2UEventService $p2uEventService)
    {
        $this->p2uEventService = $p2uEventService;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/event/p2uevent",
     *     tags={"P2U Event"},
     *     summary="Create P2U Event",
     *     description="Create a new P2U Event",
     *     security={{ "bearerAuth": {} }},
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
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="device_id",
     *                     type="string",
     *                     description="Enter Device Id",
     *                     example="1B0B308C-68CD-462D-A04C-7ABC140AAB6D"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Event created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="device_id",
     *                     type="string",
     *                     example="1B0B308C-68CD-462D-A04C-7ABC140AAB6D"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Invalid input data"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthorized"
     *             )
     *         )
     *     )
     * )
     */
    public function addDailyAttendance(Request $request)
    {
        try {
            if($this->p2uEventService->checkAttendenceForToday()) {
                return HelpersTraits::sendError(null, __('messages.already_exists'), Response::HTTP_CONFLICT);
            }

            $addDailyAttendance = $this->p2uEventService->addDailyAttendance($request);
            if ($addDailyAttendance) {
                $p2uEventDetails = new P2UEventResource($addDailyAttendance);
                return HelpersTraits::sendResponse($p2uEventDetails, __('messages.p2upoint_add'), null);
            }

            return HelpersTraits::sendError(null, __('messages.failed_p2u'), Response::HTTP_INTERNAL_SERVER_ERROR);

        } catch (\Exception $e) {
            return HelpersTraits::sendError($e->getMessage(), __('messages.error'),  Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * @OA\GET(
     *     path="/api/v1/event/fecth-p2u-event",
     *     tags={"P2U Event"},
     *     summary="Get the p2u events point records records by login user.",
     *     description="Get the p2u events point records records by login user.",
     *     security={{ "bearerAuth": {} }},
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
     *     @OA\Response(
     *         response=200,
     *         description="P2U events point  fetched successfully",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Invalid input data"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthorized"
     *             )
     *         )
     *     )
     * )
     */
    public function fetchP2UEventPoint(FilterRequest $request)
    {
        try {
            $filters = $request->filters();
            $fetchP2UEventPoint = $this->p2uEventService->fetchP2UEventPoint($filters);
            if ($fetchP2UEventPoint->isEmpty()) {
                return HelpersTraits::sendResponse(null, __('messages.p2u_not_found'),null);
            }

            $p2uEventDetails = P2UEventResource::collection($fetchP2UEventPoint);
            return HelpersTraits::sendResponse($p2uEventDetails, __('messages.p2u_fetch_successfully'), $fetchP2UEventPoint);

        } catch (\Exception $e) {
            return HelpersTraits::sendError($e->getMessage(), __('messages.error'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }


    /**
     * @OA\GET(
     *     path="/api/v1/event/sum-p2u-event",
     *     tags={"P2U Event"},
     *     summary="Fetch the sum of P2U amounts for the authenticated user",
     *     description="Create a new P2U Event",
     *     security={{ "bearerAuth": {} }},
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
     *         description="P2U Points retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="P2U Points retrieved successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="Developer"
     *                 ),
     *                 @OA\Property(
     *                     property="sum",
     *                     type="number",
     *                     format="double",
     *                     example=2000
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function fetchP2UEventPointSum()
    {
        $userId = Auth::id();
        $get_category =  $this->p2uEventService->getEventCategory(1);
        $totalP2UAmount = P2UEvent::where('user_id', $userId)->where('transfer_status','confirmed')->count();
        $totalSum =  $get_category->amount * $totalP2UAmount;
        $p2uAmountResource = p2uEventSumResource::make([$totalSum]);
        return HelpersTraits::sendResponse($p2uAmountResource, __('messages.p2u_fetch_successfully'));
    }

}
