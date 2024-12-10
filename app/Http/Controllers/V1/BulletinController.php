<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulletinRequest;
use App\Http\Requests\FilterRequest;
use App\Http\Requests\UpdateBulletin;
use App\Http\Resources\BulletinResource;
use App\Models\Bulletin;
use App\Services\BulletinService;
use App\Traits\HelpersTraits;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BulletinController extends Controller
{

    protected $bulletinService, $mediaHandler;

    public function __construct(BulletinService $bulletinService)
    {
        $this->bulletinService = $bulletinService;
    }



    /**
     * @OA\Get(
     *     path="/api/v1/bulletin",
     *     tags={"Bulletin"},
     *     summary="Get Bulletin List",
     *     description="Get Bulletin List",
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=10
     *         ),
     *         description="Number of Bulletin per page"
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
     *    @OA\Parameter(
     *          name="type",
     *          in="query",
     *          description="Select Type for Bulletin",
     *          @OA\Schema(
     *              type="string",
     *               enum={"news", "events"},
     *          )
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
     *         description="Bulletin list found",
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
     *                 example="Bulletin list found"
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
     *                 example="Bulletin List not found"
     *             )
     *         )
     *     )
     * )
     */
    public function bulletinListing(FilterRequest $request)
    {
        try {
            $filters = $request->filters();

            $user = auth()->user();
            $query = Bulletin::orderBy('id', 'DESC');
            if ($user) {
                $authRoleIds = $user->roles->pluck('id')->map(fn($id) => (string) $id)->toArray();
                $filters['permission'] = $authRoleIds;
            } else {
                $query->whereNull('permission');
            }

            $bulletins =  $query->filterAndPaginate($filters);

            if ($bulletins->isEmpty()) {
                return HelpersTraits::sendResponse(null, __('messages.not_found'), null);
            }

            $bulletinResource = BulletinResource::collection($bulletins);
            return HelpersTraits::sendResponse($bulletinResource, __('messages.list_found'), $bulletins);
        } catch (\Exception $e) {
            return HelpersTraits::sendError($e->getMessage());
        }
    }



    /**
    * @OA\POST(
    *     path="/api/v1/bulletin/create",
    *     tags={"Bulletin"},
    *     summary="Create Bulletin",
    *     description="Create a new bulletin",
    *     security={{ "bearerAuth": {} }},
    *    @OA\Parameter(
    *          name="locale",
    *          in="query",
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
    *              required={"title","distinguish", "content"},
    *                 @OA\Property(
    *                     property="title",
    *                     type="string",
    *                     description="Title for Bulletin",
    *                     example="Meeting Announcement"
    *                 ),
    *                 @OA\Property(
    *                     property="distinguish",
    *                     type="string",
    *                     enum={"General", "Important"},
    *                     description="Distinguish type for Bulletin",
    *                     example="General"
    *                 ),
    *                 @OA\Property(
    *                     property="attachment",
    *                     type="string",
    *                     format="binary",
    *                     description="Upload attachment for Bulletin"
    *                 ),
    *                @OA\Property(
    *                      property="permission",
    *                      type="array",
    *                      description="Permission levels to view Bulletin",
    *                      @OA\Items(
    *                          type="string",
    *                          example=1
    *                      )
    *                 ),
    *                @OA\Property(
    *                      property="content",
    *                      type="string",
    *                      description="Content for Bulletin",
    *                 ),
    *                  @OA\Property(
    *                      property="type",
    *                      type="string",
    *                      enum={"news", "events"},
    *                      description="Select Type for bulletin",
    *                      example="news"
    *                  ),
    *                  @OA\Property(
    *                      property="due_date",
    *                      type="string",
    *                      format="date",
    *                      example="2024-12-31",
    *                      description="Due Date for Bulletin in YYYY-MM-DD format"
    *                  ),
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Bulletin created successfully",
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
    *                     property="title",
    *                     type="string",
    *                     example="Meeting Announcement"
    *                 ),
    *                 @OA\Property(
    *                     property="distinguish",
    *                     type="string",
    *                     example="중요"
    *                 ),
    *                 @OA\Property(
    *                     property="attachment",
    *                     type="string",
    *                     example="file.pdf"
    *                 ),
    *                 @OA\Property(
    *                     property="permission",
    *                     type="integer",
    *                     example=1
    *                 )
    *             ),
    *             @OA\Property(
    *                 property="message",
    *                 type="string",
    *                 example="Bulletin created successfully"
    *             ),
    *              @OA\Property(
    *                  property="type",
    *                  type="string",
    *                  example="news"
    *              ),
    *              @OA\Property(
    *                 property="due_date",
    *                 type="string",
    *                 description="DD-MM-YYYY",
    *              ),
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

    public function create(BulletinRequest $request)
    {
        try {
            $bulletin = $this->bulletinService->createBulletin($request);
            $bulletin = new BulletinResource($bulletin);
            return HelpersTraits::sendResponse($bulletin, __('messages.bulletin_create'), null);
        } catch (\Exception $e) {
            return HelpersTraits::sendError($e->getMessage(), __('messages.error'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
    * @OA\POST(
    *     path="/api/v1/bulletin/{id}/update",
    *     tags={"Bulletin"},
    *     summary="Update Bulletin",
    *     description="Update an existing bulletin",
    *     security={{ "bearerAuth": {} }},
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         required=true,
    *         @OA\Schema(
    *             type="integer"
    *         ),
    *         description="ID of the bulletin to update"
    *     ),
    *     @OA\Parameter(
    *          name="locale",
    *          in="query",
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
    *              required={"title","distinguish", "content"},
    *                 @OA\Property(
    *                     property="title",
    *                     type="string",
    *                     description="Title for Bulletin",
    *                     example="Updated Meeting Announcement"
    *                 ),
    *                 @OA\Property(
    *                     property="distinguish",
    *                     type="string",
    *                     enum={"General", "Important"},
    *                     description="Distinguish type for Bulletin",
    *                     example="중요"
    *                 ),
    *                 @OA\Property(
    *                     property="attachment",
    *                     type="string",
    *                     format="binary",
    *                     description="Upload attachment for Bulletin"
    *                 ),
    *                @OA\Property(
    *                      property="permission",
    *                      type="array",
    *                      description="Permission levels to view Bulletin",
    *                      @OA\Items(
    *                          type="string",
    *                          example=1
    *                      )
    *                 ),
    *                 @OA\Property(
    *                      property="content",
    *                      type="string",
    *                      description="Content for Bulletin",
    *                 )
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Bulletin updated successfully",
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
    *                     property="title",
    *                     type="string",
    *                     example="Updated Meeting Announcement"
    *                 ),
    *                 @OA\Property(
    *                     property="distinguish",
    *                     type="string",
    *                     example="중요"
    *                 ),
    *                 @OA\Property(
    *                     property="attachment",
    *                     type="string",
    *                     example="updated_file.pdf"
    *                 ),
    *                 @OA\Property(
    *                     property="permission",
    *                     type="integer",
    *                     example=1
    *                 )
    *             ),
    *             @OA\Property(
    *                 property="message",
    *                 type="string",
    *                 example="Bulletin updated successfully"
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
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Bulletin not found",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(
    *                 property="message",
    *                 type="string",
    *                 example="Bulletin not found"
    *             )
    *         )
    *     )
    * )
    */
    public function update(UpdateBulletin $request, $id)
    {
        try {
            $bulletin = $this->bulletinService->updateBulletin($request, $id);
            $bulletin = new BulletinResource($bulletin);
            return HelpersTraits::sendResponse($bulletin, __('messages.bulletin_update'));
        } catch (\Exception $e) {
            return HelpersTraits::sendError($e->getMessage(), __('messages.error'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
    * @OA\DELETE(
    *     path="/api/v1/bulletin/{id}/delete",
    *     tags={"Bulletin"},
    *     summary="Delete Bulletin",
    *     description="Delete an existing bulletin",
    *     security={{ "bearerAuth": {} }},
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         required=true,
    *         @OA\Schema(
    *             type="integer"
    *         ),
    *         description="ID of the bulletin to delete"
    *     ),
    *     @OA\Parameter(
    *          name="locale",
    *          in="query",
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
    *         description="Bulletin deleted successfully",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(
    *                 property="message",
    *                 type="string",
    *                 example="Bulletin deleted successfully"
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
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Bulletin not found",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(
    *                 property="message",
    *                 type="string",
    *                 example="Bulletin not found"
    *             )
    *         )
    *     )
    * )
    */
    public function delete($id)
    {
        try {
            $bulletin = $this->bulletinService->deleteBulletin($id);
            return HelpersTraits::sendResponse($bulletin, __('messages.bulletin_delete'), null);
        } catch (\Exception $e) {
            return HelpersTraits::sendError($e->getMessage(), __('messages.error'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    /**
     * @OA\Get(
     *     path="/api/v1/bulletin/{id}/view",
     *     tags={"Bulletin"},
     *     summary="View Bulletin",
     *     description="Get Bulletin View",
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Bulletin Id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *          name="Accept-Language",
     *          in="header",
     *          description="Language code for language selection",
     *          required=false,
     *          @OA\Schema(
     *              type="string",
     *              enum=LOCALE_ENUM,
     *              default="ko",
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bulletin view found",
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
     *                 example="Bulletin view found"
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
     *                 example="Bulletin view not found"
     *             )
     *         )
     *     )
     * )
     */
    public function view(FilterRequest $request, $id)
    {
       try {
            $filters = [];
            $query = Bulletin::where('id', $id)->whereNull('deleted_at');
            if(auth()->check() && !auth()->user()->isAdmin()) {
                $authRoleIds = auth()->user()->roles->pluck('id')->map(function ($id) {
                    return (string) $id;
                })->toArray();

                $filters['permission'] = $authRoleIds;
            }else{
                $query->whereNull('permission');
            }

            $bulletin = $query->filter($filters)->first();

            if($bulletin){
                $bulletin = new BulletinResource($bulletin);
                return HelpersTraits::sendResponse($bulletin, __('messages.bulletin_found'), null);
            }else{
                return HelpersTraits::sendResponse(null, __('messages.bulletin_not_found'), null);
            }

       } catch (\Throwable $e) {
           return HelpersTraits::sendError($e->getMessage(), __('messages.error'), Response::HTTP_INTERNAL_SERVER_ERROR);
       }

    }


}
