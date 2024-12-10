<?php

namespace App\Http\Controllers\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManageHomepageRequest;
use App\Http\Resources\MyInfoResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserSettingResource;
use App\Models\User;
use App\Models\UserSetting;
use App\Traits\HelpersTraits;
use App\Traits\MediaHandler;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MyInfoController extends Controller
{
    use MediaHandler;

    /**
     * @OA\Post(
     *     path="/api/v1/my-info/manage/update",
     *     summary="Update current user's my-info details",
     *     security={{ "bearerAuth": {} }},
     *     tags={"MyInfo"},
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
     *             @OA\Schema(ref="#/components/schemas/ManageHomepageRequest")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ManageHomepageRequest"
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

    public function manageUpdate(ManageHomepageRequest $request)
    {
        $user = user();
        // abort update if required permissions are not available
        if (auth()->id() != $user->id) {
            throw new AuthorizationException();
        }

        DB::beginTransaction();

        try {
            $userSetting = $user->userSetting()->firstOrNew();
            $userSetting->fill([
                'image_register'    => $request->input('is_sale_person_image_enable'),
                'text_register'     => $request->input('is_bio_enable'),
                'email'             => $request->input('contact_email'),
                'telephone'         => $request->input('contact_number'),
                'text_registration' => $request->input('bio_text'),
                'portfolio'         => $request->input('portfolio'),
                'sns'               => $this->prepareSnsData($request),
            ])->save();

            if ($request->hasFile('sales_person_image')) {
                $salesPersonImg = $this->uploadSalesPerson($request->file('sales_person_image'));
                $user->syncMedia($salesPersonImg, 'sales-person');
            }

            $userSetting = UserSettingResource::make(['user_setting' => $userSetting, 'sales_person_image' => $user->salesPersonImage()]);

            DB::commit();
            return HelpersTraits::sendResponse($userSetting, __('messages.my_info_user_setting_updated'));
        } catch (\Exception $e) {
            DB::rollBack();
            return HelpersTraits::sendError($e->getMessage());
        }
    }


    /**
     * @OA\Post(
     *     path="/api/v1/my-info/update-channel-order",
     *     summary="Update current user's my-info details",
     *     security={{ "bearerAuth": {} }},
     *     tags={"MyInfo"},
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
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *                   @OA\Schema(
     *                 type="object",
     *                 required={"channel_order_data"},
     *                 @OA\Property(
     *                     property="channel_order_data",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="channel_id", type="string", example="49"),
     *                         @OA\Property(property="order", type="integer", example=31),
     *                         required={"channel_id", "order"}
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ManageHomepageRequest"
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
     * )
     */
    public function updateChannelOrder(Request $request)
    {
        $request->validate([
            'channel_order_data' => 'required|array',
            'channel_order_data.*' => 'required|array',
            'channel_order_data.*.channel_id' => 'required|exists:channels,id',
            'channel_order_data.*.order' => 'required|integer',
        ]);
        //send the orderings along with the channels
        $user = user();
        try {
            $newData = $request->input('channel_order_data');

            if ($user->userSetting) {
                $user->userSetting()->update(['product_ordering' => $newData]);
                return HelpersTraits::sendResponse(['channel_order' => json_decode($user->userSetting->product_ordering)], __('messages.my_info_channel_order_updated'));
            } else {
                throw new AuthorizationException();
            }
        } catch (\Exception $e) {
            return HelpersTraits::sendError($e->getMessage());
        }
    }

    // helper methods
    private function prepareSnsData(Request $request)
    {
        $all_url = [
            'facebook'  => [
                'status' => $request->input('fa_status'),
                'url'    => $request->input('facebook_url'),
            ],
            'instagram' => [
                'status' => $request->input('in_status'),
                'url'    => $request->input('instagram_url'),
            ],
            'kakaotalk' => [
                'status' => $request->input('ko_status'),
                'url'    => $request->input('kakaotalk_url'),
            ],
            'blog'      => [
                'status' => $request->input('bl_status'),
                'url'    => $request->input('blog_url'),
            ],
        ];
        return json_encode($all_url);
    }
}
