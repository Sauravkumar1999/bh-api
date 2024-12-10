<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRequest;
use App\Http\Requests\MonthlyNewsRequest;
use App\Http\Resources\MonthlyNewsResource;
use App\Models\MonthlyNews;
use App\Services\MonthlyNewsService;
use App\Traits\HelpersTraits;
use Illuminate\Http\Response;

class MonthlyNewsController extends Controller
{
    use HelpersTraits;

    public function __construct(private MonthlyNewsService $service)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/monthly-news",
     *     tags={"Monthly News"},
     *     summary="Get Monthly News List",
     *     description="Get Monthly News List",
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=10
     *         ),
     *         description="Number of Monthly News per page"
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
     *         description="Monthly News list found",
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
     *                 example="Users list found"
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
     *                 example="Monthly News List not found"
     *             )
     *         )
     *     )
     * )
     */

    public function index(FilterRequest $request)
    {
        try {
            $filters = $request->filters();
            $news = MonthlyNews::orderBy('posting_date', 'desc')->filterAndPaginate($filters);
            if ($news->isEmpty()) {
                return HelpersTraits::sendResponse(null, __('messages.not_found'), null);
            }
            $newsResource  = MonthlyNewsResource::collection($news);
            return HelpersTraits::sendResponse($newsResource, __('messages.found'), $newsResource);
        } catch (\Exception $e) {
            return HelpersTraits::sendError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/monthly-news/create",
     *     summary="Create a new Monthly News",
     *     security={{ "bearerAuth": {} }},
     *     tags={"Monthly News"},
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
     *         @OA\JsonContent(ref="#/components/schemas/MonthlyNewsRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Monthly News successfully created",
     *         @OA\JsonContent(ref="#/components/schemas/MonthlyNewsResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     )
     * )
     */
    public function store(MonthlyNewsRequest $request){
        if(!$news = $this->service->createOrUpdate($request)){
            return HelpersTraits::sendError($this->service->error_message, [], Response::HTTP_BAD_REQUEST);
        }

        return HelpersTraits::sendResponse(new MonthlyNewsResource($news), __('messages.news_created'));
    }

    /**
     * @OA\Put(
     *     path="/api/v1/monthly-news/{news_id}/update",
     *     summary="Update an existing Monthly News",
     *     security={{ "bearerAuth": {} }},
     *     tags={"Monthly News"},
     *     @OA\Parameter(
     *         name="news_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the Monthly News to update"
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
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/MonthlyNewsRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role successfully updated",
     *         @OA\JsonContent(ref="#/components/schemas/MonthlyNewsResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     )
     * )
     */
    public function update(MonthlyNewsRequest $request, $news_id){
        if(!$news = $this->service->createOrUpdate($request, $news_id)){
            return HelpersTraits::sendError($this->service->error_message, Response::HTTP_BAD_REQUEST);
        }
        return HelpersTraits::sendResponse(new MonthlyNewsResource($news), __('messages.news_updated'));
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/monthly-news/{news_id}/delete",
     *     summary="Delete an existing Monthly News",
     *     security={{ "bearerAuth": {} }},
     *     tags={"Monthly News"},
     *     @OA\Parameter(
     *         name="news_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the role to delete"
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
     *         description="Role successfully deleted",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Role successfully deleted"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     )
     * )
     */
    public function destroy($news_id){
        if(!$this->service->destroy($news_id)){
            return HelpersTraits::sendError($this->service->error_message, Response::HTTP_BAD_REQUEST);
        }
        return HelpersTraits::sendResponse(null, __('messages.news_deleted'));
    }

    /**
     * @OA\GET(
     *     path="/api/v1/monthly-news/{news_id}/single",
     *     summary="Get Monthly News with news Id",
     *     security={{ "bearerAuth": {} }},
     *     tags={"Monthly News"},
     *     @OA\Parameter(
     *         name="news_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Enter Monthly News Id"
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
     *         description="Monthly News Found successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Monthly News Found successfully"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     )
     * )
     */

    public function getSingle($news_id)
    {
        $data = $this->service->getSingle($news_id);

        if ($data['success'] === true) {
            return HelpersTraits::sendResponse($data['news'], __('messages.news_found'));
        }

        return HelpersTraits::sendError($data['message'], [], Response::HTTP_UNAUTHORIZED);
    }
}
