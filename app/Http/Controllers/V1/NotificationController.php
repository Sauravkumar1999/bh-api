<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Http\Requests\FilterRequest;
use App\Http\Requests\AddInternalUrlRequest;
use App\Http\Resources\NotificationResource;
use App\Traits\HelpersTraits;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing notification of the current login user.
     *
     * @OA\Get(
     *     path="/api/v1/notifications",
     *     tags={"Notifications"},
     *     summary="Get a list of login users notifications",
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=10
     *         ),
     *         description="Number of user notifications per page"
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
     *      @OA\Response(
     *         response=200,
     *         description="Notification found",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found",
     *         @OA\JsonContent(type="object")
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */    
    public function index(FilterRequest $request)
    {
        $data = $this->notificationService->getAllNotifications($request);

        if($data['success'] === false){
            return HelpersTraits::sendError($data['message']);
        }

        $notifications = NotificationResource::collection($data['data']);
        return HelpersTraits::sendResponse($notifications,$data['message'],$data['data']);
    }

    /**
     * Store a new internal or external URL for a notification.
     *
     * @OA\Post(
     *     path="/api/v1/notifications/internal-url/add",
     *     tags={"Notifications"},
     *     summary="Add a new internal URL for a notification",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="url", type="string", description="The URL to be added"),
     *                 @OA\Property(property="type", type="string", description="The type of URL, either 'internal' or 'external'"),
     *                 @OA\Property(property="name", type="string", description="Optional name for the URL")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="URL added successfully",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(type="object")
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function addRedirectUrl(AddInternalUrlRequest $request)
    {
        if (is_admin_user()) {

            $result = $this->notificationService->addUrl($request);

            if ($result['success'] === false) {
                return HelpersTraits::sendError($result['message'], []);
            }

            return HelpersTraits::sendResponse($result['data'], $result['message']);
        } else{
            return HelpersTraits::sendError(__('messages.access_rights_not_found'), []);
        }
    }


    /**
     * Display the specified notification.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/api/v1/notifications/{id}/view",
     *     tags={"Notifications"},
     *     summary="Get a single notification",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Notification id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Notification found."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Record not found",
     *         @OA\JsonContent(type="object")
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */    
    public function view($id)
    {
        $data = $this->notificationService->getNotification($id);

        if($data['success'] === false){
            return HelpersTraits::sendError($data['message']);
        }

        return HelpersTraits::sendResponse($data['data'],$data['message']);
    }
}
