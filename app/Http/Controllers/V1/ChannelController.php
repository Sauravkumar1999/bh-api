<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChannelRequest;
use App\Http\Requests\FilterRequest;
use App\Http\Resources\ChannelResource;
use App\Models\Channel;
use App\Traits\HelpersTraits;
use App\Services\ChannelService;
use Symfony\Component\HttpFoundation\Response;


class ChannelController extends Controller
{

    protected $channelService;

    public function __construct(ChannelService $channelService)
    {
        $this->channelService = $channelService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/channels",
     *      tags={"Channel"},
     *      summary="Get Channel List",
     *      description="Get Channel List",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Parameter(
     *          name="per_page",
     *          in="query",
     *          required=false,
     *          @OA\Schema(
     *              type="integer",
     *              default=10
     *          ),
     *          description="Number of Channels per page"
     *      ),
     *       @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=1
     *         ),
     *         description="Page Number"
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="Channel List Found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(type="object")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="Channel list found"
     *              ),
     *              @OA\Property(
     *                  property="meta",
     *                  type="object",
     *                  @OA\Property(
     *                      property="pagination",
     *                      type="object",
     *                      @OA\Property(
     *                         property="total",
     *                         type="integer",
     *                         example=100
     *                      ),
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
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *         response=404,
     *         description="Channel List not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Channel List not found"
     *             )
     *         )
     *      )
     * )
     */
    public function index(FilterRequest $request)
    {
        $filters = $request->filters();

        $channels = Channel::orderBy('id', 'DESC')
            ->filterAndPaginate($filters);

        if ($channels->isEmpty()) {
            return HelpersTraits::sendError(null, __('messages.channel_not_found'));
        }

        $channel = ChannelResource::collection($channels);

        return HelpersTraits::sendResponse($channel, __('messages.channel_found'), $channels);
    }


    /**
     * @OA\Post(
     *     path="/api/v1/channels/create",
     *     summary="Create a new Channel",
     *     security={{ "bearerAuth": {} }},
     *     tags={"Channel"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *              encoding={
     *                  "approval_rights[]": {
     *                      "explode": true,
     *                  },
     *                  "sr[]": {
     *                      "explode": true,
     *                  },
     *                  "url_params[]": {
     *                      "explode": true,
     *                  },
     *              },
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/ChannelRequest")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="successfully stored")
     *         )
     *     ),
     *      @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function create(ChannelRequest $request)
    {
        $data = $this->channelService->createChannel($request);

        if ($data['success'] == true) {
            return HelpersTraits::sendResponse($data['data'], __('messages.channel_created'));
        }

        return HelpersTraits::sendError($data['message'], [], Response::HTTP_UNAUTHORIZED);
    }



    /**
     * @OA\Post(
     *     path="/api/v1/channels/{id}/update",
     *     summary="Update an existing channel",
     *     security={{ "bearerAuth": {} }},
     *     tags={"Channel"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the channel to update",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            encoding={
     *                  "approval_rights[]": {
     *                      "explode": true,
     *                  },
     *                  "sr[]": {
     *                      "explode": true,
     *                  },
     *                  "url_params[]": {
     *                      "explode": true,
     *                  },
     *             },
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/ChannelRequest")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ChannelRequest"
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     * )
     */
    public function update(ChannelRequest $request, Channel $id)
    {
        $data = $this->channelService->updateChannel($request, $id);

        if ($data['success'] == true) {
            return HelpersTraits::sendResponse($data['data'], __('messages.channel_updated'));
        }

        return HelpersTraits::sendError($data['message'], [], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/channels/{id}/delete",
     *     summary="Delete a channel",
     *     security={{ "bearerAuth": {} }},
     *     tags={"Channel"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the channel to delete",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Channel deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $data = $this->channelService->deleteChannel($id);

        if ($data['success'] == true) {
            return HelpersTraits::sendResponse($data, __('messages.channel_deleted'));
        }

        return HelpersTraits::sendError($data['message'], [], Response::HTTP_UNAUTHORIZED);
    }
}
