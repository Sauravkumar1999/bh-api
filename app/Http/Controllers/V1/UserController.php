<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRequest;
use App\Http\Requests\FindPasswordResetRequest;
use App\Http\Requests\FindPasswordVerifyRequest;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserSettingUpdateRequest;
use App\Http\Requests\UserSingleRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\MyInfoResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use App\Traits\HelpersTraits;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    use HelpersTraits;

    public function __construct(private UserService $service)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/users",
     *     tags={"User"},
     *     summary="Get Users List",
     *     description="Get Users List",
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=10
     *         ),
     *         description="Number of Users per page"
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
     *         name="role",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             default="Developer"
     *         ),
     *         description="User role"
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="boolen",
     *             default=1
     *         ),
     *         description="user status"
     *     ),
     *     @OA\Parameter(
     *         name="royal_member_application",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *         description="user is royal member or not"
     *     ),
     *     @OA\Parameter(
     *         name="confirm_start_date",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             format="date",
     *             example="2024-06-01"
     *         ),
     *         description="Confirmation start date"
     *     ),
     *     @OA\Parameter(
     *         name="confirm_end_date",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             format="date",
     *             example="2024-06-30"
     *         ),
     *         description="Confirmation end date"
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
     *         description="Users list found",
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
     *                 example="Users List not found"
     *             )
     *         )
     *     )
     * )
     */

    public function index(FilterRequest $request)
    {
        try {
            $filters = $request->filters();
            $users = User::orderBy('first_name', 'asc')->orderBy('last_name', 'asc')->filterAndPaginate($filters);
            if ($users->isEmpty()) {
                return HelpersTraits::sendResponse(null, __('messages.not_found'), null);
            }
            $userResource = UserResource::collection($users);
            return HelpersTraits::sendResponse($userResource, __('messages.found'), $userResource);
        } catch (\Exception $e) {
            return HelpersTraits::sendError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/users/get-channel/{user_id}",
     *     summary="Get User Channel Setting",
     *     description="User Channel Setting by ID.",
     *     tags={"User"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         description="ID of the user",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
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
     *         description="User Product Setting found successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User Product Setting not found"),
     *             @OA\Property(property="success", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error message"),
     *             @OA\Property(property="success", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized access"),
     *             @OA\Property(property="success", type="boolean", example=false)
     *         )
     *     )
     * )
     */

    public function getChannel($user_id)
    {
        $data = $this->service->getChannel($user_id);
        if ($data['success'] === true) {
            return HelpersTraits::sendResponse($data, __('messages.found', ['attribute' => __('attributes.user')]));
        }

        return HelpersTraits::sendError($data['message'], [], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/users/update-channel/{user_id}",
     *     tags={"User"},
     *     security={{ "bearerAuth": {} }},
     *     summary="Update user channel setting",
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
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
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/UserSettingUpdateRequest")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="channel setting updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="updated"),
     *             @OA\Property(property="success", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function updateChannelSettings(UserSettingUpdateRequest $request, $user_id)
    {
        $data = $this->service->updateChannelSettings($request->validated(), $user_id);
        if ($data['success']) {
            return HelpersTraits::sendResponse($data['products'], __('messages.record_updated'));
        }
        return HelpersTraits::sendError($data['message']);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/find-password/verify",
     *     tags={"Authentication"},
     *     summary="Find Password Verify User",
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
     *             @OA\Schema(ref="#/components/schemas/FindPasswordVerifyRequest")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User Verified",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="User not found"
     *     )
     * )
     */
    public function verify(FindPasswordVerifyRequest $request)
    {
        $data = $this->service->verifyUser($request->validated());

        if ($data['success'] === true) {
            return HelpersTraits::sendResponse($data, __('messages.user_verified'));
        }

        return HelpersTraits::sendError($data['message'], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/find-password/{id}/reset",
     *     tags={"Authentication"},
     *     summary="Find Password Reset Password",
     *        @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="User ID"
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
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/FindPasswordResetRequest")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User Verified",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="User not found"
     *     )
     * )
     */
    public function reset(FindPasswordResetRequest $request, $id)
    {
        $data = $this->service->resetPassword($request->validated(), $id);
        if ($data['success'] === true) {
            return HelpersTraits::sendResponse($data, __('messages.password_reset_success'));
        }
        return HelpersTraits::sendError($data['message'], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/user/create",
     *     tags={"User"},
     *     security={{ "bearerAuth": {} }},
     *     summary="Create a new user",
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
     *             @OA\Schema(ref="#/components/schemas/UserCreateRequest")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful Created",
     *         @OA\JsonContent(type="object",
     *              @OA\Property(property="user", type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="first_name", type="string"),
     *                  @OA\Property(property="email", type="string"),
     *                  @OA\Property(property="phone", type="string"),
     *                  @OA\Property(property="dob", type="string", format="date"),
     *                  @OA\Property(property="gender", type="string"),
     *                  @OA\Property(property="account_number", type="string"),
     *                  @OA\Property(property="state", type="string"),
     *                  @OA\Property(property="post_code", type="string"),
     *                  @OA\Property(property="address", type="string"),
     *                  @OA\Property(property="address_detail", type="string")
     *              )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */

    public function create(UserCreateRequest $request)
    {
        $data = $this->service->createUser($request);
        if ($data['success'] === true) {
            return HelpersTraits::sendResponse($data['user'], __('messages.create_success'));
        }

        return HelpersTraits::sendError($data['message'], [], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/user/{user_id}/update",
     *     tags={"User"},
     *     security={{ "bearerAuth": {} }},
     *     summary="Update an existing user",
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
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
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/UserUpdateRequest")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully Updated",
     *         @OA\JsonContent(type="object",
     *              @OA\Property(property="user", type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="first_name", type="string"),
     *                  @OA\Property(property="email", type="string"),
     *                  @OA\Property(property="phone", type="string"),
     *                  @OA\Property(property="dob", type="string", format="date"),
     *                  @OA\Property(property="gender", type="string"),
     *                  @OA\Property(property="account_number", type="string"),
     *                  @OA\Property(property="state", type="string"),
     *                  @OA\Property(property="post_code", type="string"),
     *                  @OA\Property(property="address", type="string"),
     *                  @OA\Property(property="address_detail", type="string")
     *              )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */

    public function update(UserUpdateRequest $request, $id)
    {
        $data = $this->service->updateUser($request, $id);
        if ($data['success'] === true) {
            return HelpersTraits::sendResponse($data['user'], __('messages.updated_success'));
        }

        return HelpersTraits::sendError($data['message'], [], Response::HTTP_UNAUTHORIZED);
    }


    /**
     * @OA\GET(
     *     path="/api/v1/user/{user_id}",
     *     tags={"User"},
     *     security={{ "bearerAuth": {} }},
     *     summary="Get a single user",
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
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
     *         description="User successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User successfully retrieved"),
     *             @OA\Property(property="success", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error message"),
     *             @OA\Property(property="success", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized access"),
     *             @OA\Property(property="success", type="boolean", example=false)
     *         )
     *     )
     * )
     */

    public function singleUser(UserSingleRequest $request)
    {
        $data = $this->service->singleUser($request->user_id);

        if ($data['success'] === true) {
            return HelpersTraits::sendResponse($data['user'], __('messages.user_found'));
        }

        return HelpersTraits::sendError($data['message'], [], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @OA\DELETE(
     *     path="/api/v1/user/delete",
     *     summary="Delete a user",
     *     description="Deletes a user by ID and marks their status as inactive.",
     *     tags={"User"},
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
     *         description="User deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User deleted"),
     *             @OA\Property(property="success", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error message"),
     *             @OA\Property(property="success", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized access"),
     *             @OA\Property(property="success", type="boolean", example=false)
     *         )
     *     )
     * )
     */

    public function delete()
    {
        if (user()->isAdmin()) {
            return HelpersTraits::sendError(__('messages.user_is_admin'), [], Response::HTTP_UNAUTHORIZED);
        }
        if (auth()->user()) {
            $id = auth()->user()->id;
            $data = $this->service->deleteUser($id);

            if ($data['success'] === true) {
                return HelpersTraits::sendResponse(
                    $data['user'],
                    __('messages.delete_success', ['attribute' => __('attributes.user')])
                );
            }
        }
        return HelpersTraits::sendError($data['message'], [], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @OA\POST(
     *     path="/api/v1/user/activate-deleted",
     *     tags={"User"},
     *     security={{ "bearerAuth": {} }},
     *     summary="Activate the given users with email adresses",
     *     @OA\Parameter(
     *         name="user_emails[]",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="array", @OA\Items(type="string"))
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
     *         description="Users activated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User activated"),
     *             @OA\Property(property="success", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error message"),
     *             @OA\Property(property="success", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized access"),
     *             @OA\Property(property="success", type="boolean", example=false)
     *         )
     *     )
     * )
     */
    public function activateDeleted(Request $request)
    {
        if (auth()->check() && user()->isAdmin()) {

            $users = $request->get('user_emails');

            if (count($users) > 0) {

               array_walk($users, fn(&$x) => $x = "'$x'");

                DB::statement('UPDATE users SET `users`.`deleted_at` = NULL, `users`.`status` = 1 WHERE `users`.`deleted_at` IS NOT NULL AND `users`.`email` IN ('.implode(',', $users).')');
                return HelpersTraits::sendResponse('', __('Users restored successfully !'));

            } else {

                return HelpersTraits::sendError(__('No any valid emails provided.'), [], Response::HTTP_UNAUTHORIZED);
            }


        } else {
            return HelpersTraits::sendError(__('Not have proper privileges to perform this task.'), [], Response::HTTP_UNAUTHORIZED);
        }
    }
     /**
     * @OA\Get(
     *     path="/api/v1/user/{user_code}",
     *     tags={"User"},
     *     summary="Get User details",
     *     description="Get User details based on user code",
     *     @OA\Parameter(
     *         name="user_code",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *         description="User code to fetch the details"
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
     *         description="User found",
     *      ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid code",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="User not found"
     *             )
     *         )
     *     )
     * )
     */
    public function getUser(Request $request)
    {
        try {
            if ($request->user_code) {
                $user = User::where('code', $request->user_code)->first();
                if ($user && $user->status === 1) {
                    $userResource = MyInfoResource::make(['user' => $user?->load(['userSetting'])]);
                    return HelpersTraits::sendResponse($userResource, __('messages.user_found'));
                } else {
                    return HelpersTraits::sendError(__('messages.user_not_found'));
                }
            }
            return HelpersTraits::sendError(__('messages.user_not_found'), [], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return HelpersTraits::sendError($e->getMessage());
        }
    }
}
