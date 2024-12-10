<?php

namespace App\Http\Controllers\V1;

use App\Exceptions\InvalidCredentialsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmailAvailableRequest;
use App\Http\Requests\RefreshRequest;
use App\Http\Requests\VerifyUserRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\OTPRequest;
use App\Http\Requests\ReferralCodeRequest;
use App\Http\Resources\MyInfoResource;
use App\Models\User;
use App\Services\AuthenticationService;
use App\Services\SessionService;
use App\Services\SMSService;
use App\Traits\HelpersTraits;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Response;
use App\Exceptions\UserNotFoundException;
use App\Http\Requests\FCMTokenRequest;
use App\Models\DeviceToken;
use Illuminate\Http\Request;

/**
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="API Endpoints for Authentication"
 * )
 */

class AuthController extends Controller
{
    use ThrottlesLogins;
    protected $sessionService;
    protected $sMSService;

    public function __construct(private AuthenticationService $auth, SessionService $sessionService,  SMSService $sMSService)
    {

        $this->sessionService = $sessionService;
        $this->sMSService = $sMSService;

    }

    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     tags={"Authentication"},
     *     summary="Login a user",
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
     *             @OA\Schema(ref="#/components/schemas/LoginRequest")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials"
     *     )
     * )
     */
    public function login(LoginRequest $request)
    {
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            $this->sendLockoutResponse($request);
        }

        try {
            if ($this->auth->checkAccountDeleted($request->email)) {
                return HelpersTraits::sendError(__('user::authentication.deactivated'), [], 401);
            }

            if (!$this->auth->checkUserApproved($request->email)) {
                return HelpersTraits::sendError(__('user::authentication.inactive'), [], 401);
            }

            $login_data = $this->auth->attemptLogin($request->email, $request->password);

            if($request->fcm_token){
                DeviceToken::updateOrCreate(
                    [
                        'user_id' => $login_data['user_data']->id,
                        'uuid' => $request->uuid,
                    ],
                    [
                        'fcm_token' => $request->fcm_token,
                    ]
                );
            }
            $cookie = $login_data['cookie'];
            unset($login_data['cookie']);

            return HelpersTraits::sendResponse($login_data, __('messages.found'))->withCookie($cookie);
        } catch (InvalidCredentialsException $e) {
            $this->incrementLoginAttempts($request);
            return HelpersTraits::sendError(__('messages.invalid_credentials'), [], 401);
        } catch (UserNotFoundException $e) {
            // Handle non-existing email case
            $this->incrementLoginAttempts($request);
            return HelpersTraits::sendError(__('messages.invalid_credentials'), [], 401);
        } catch (\Exception $e) {
            // For any other exceptions, return a generic error response
            return HelpersTraits::sendError(__('messages.generic_error'), [], 500);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/v1/update/fcm-token",
     *     tags={"Authentication"},
     *     summary="Update fcm token for user",
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
     *             @OA\Schema(ref="#/components/schemas/FCMTokenRequest")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials"
     *     )
     * )
     */
    public function updateFCMToken(FCMTokenRequest $request)
    {
        $user = user();

        try {
            DeviceToken::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'uuid' => $request->uuid,
                ],
                [
                    'fcm_token' => $request->fcm_token,
                ]
            );
            return HelpersTraits::sendResponse([], __('messages.fcm_token_updated'));
        } catch (\Exception $e) {
            // For any other exceptions, return a generic error response
            return HelpersTraits::sendError(__('messages.generic_error'), $e->getMessage(), 500);
        }
    }



    /**
     * @OA\Post(
     *     path="/api/v1/login/refresh",
     *     tags={"Authentication"},
     *     summary="Refresh the access token",
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
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(ref="#/components/schemas/RefreshRequest")
     *          )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful refresh",
     *         @OA\JsonContent(type="object")
     *     )
     * )
     */

    public function refresh(RefreshRequest $request)
    {
        $data = $this->auth->attemptRefresh($request->refresh_token);

        return response()->json($data, $data['status_code']);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/logout",
     *     tags={"Authentication"},
     *     summary="Logout the user",
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
     *          name="uuid",
     *          in="query",
     *          description="uuid for logout",
     *          required=false,
     *          @OA\Schema(
     *              type="string",
     *          )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Successful logout"
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $data = $this->auth->logout();
        if ($data['success']) {
            if($request->uuid){
                DeviceToken::where([
                    'user_id' => $data['user']->id,
                    'uuid' => $request->uuid,
                ])->delete();
            }
            return HelpersTraits::sendResponse($data['user'], __('messages.user_logout'));
        }
        return HelpersTraits::sendError($data['message'], Response::HTTP_BAD_REQUEST);


        $this->auth->logout();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/me",
     *     tags={"Authentication"},
     *     summary="Get Current User's info",
     *     description="Current User's info.",
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
     *      @OA\Response(
     *          response=200,
     *          description="My Info Details Found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="My Info found"
     *              ),
     *              @OA\Property(
     *                  property="channels",
     *                  type="object",
     *                  @OA\Property(
     *                      property="data",
     *                      type="array",
     *                      @OA\Items(type="object")
     *                  ),
     *              ),
     *          )
     *          )
     *      ),
     *     @OA\Response(
     *         response=422,
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
    public function me(Request $request)
    {
        $user = user();

        // if logged user is not pro admin, user manage page can access to its own user only
        if (auth()->id() != $user->id) {
            throw new AuthorizationException();
        }

        try {
            $userChannels = $user?->company?->channelRights;
            $mergedChannels = $user?->company ? merge_user_settings($user, $userChannels) : collect();

            if (!$user->code) {
                return HelpersTraits::sendError(null, __('messages.not_found'));
            }

            $channelList = MyInfoResource::make(['user' => $user?->load(['userSetting']), 'channels' => $mergedChannels]);

            return HelpersTraits::sendResponse($channelList, __('messages.found'));
        } catch (\Exception $e) {
            return HelpersTraits::sendError($e->getMessage());
        }
    }

    /**
     * For the throttle middleware.
     */
    protected function username(): string
    {
        return 'email';
    }

    /**
     * @OA\Post(
     *     path="/api/v1/email_available/check",
     *     tags={"Authentication"},
     *     summary="Check email exits",
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
     *         @OA\JsonContent(ref="#/components/schemas/EmailAvailableRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email login",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid email"
     *     )
     * )
     */
    public function emailAvailable(EmailAvailableRequest $request)
    {
        $request->validated();
        return HelpersTraits::sendResponse(null, __('messages.email_available'));
    }
    /**
     * @OA\Post(
     *     path="/api/v1/referral_code/check",
     *     tags={"Authentication"},
     *     summary="Check code exits",
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
     *         @OA\JsonContent(ref="#/components/schemas/ReferralCodeRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Codeis valid",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid code"
     *     )
     * )
     */
    public function verifyReferralCode(ReferralCodeRequest $request)
    {
        $validated_data = $request->validated();
        $user = User::where('code', $validated_data['referral_code'])->select('first_name', 'last_name', 'username')->first();
        if ($user) {
            return HelpersTraits::sendResponse($user, __('messages.referral_code_available'));
        }
        return HelpersTraits::sendError(__('messages.referral_code_not_available'));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/send_otp",
     *     tags={"Authentication"},
     *     summary="Send OTP to the user",
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
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="country_code", type="string", example="82"),
     *             @OA\Property(property="phone", type="string", example="8112656567"),
     *             @OA\Property(property="name", type="string", example="user1"),
     *             @OA\Property(property="type", type="string", example="find-password"),
     *             @OA\Property(property="id", type="string", example="developer@developer.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function sendOTP(OTPRequest $request)
    {
        try {

            if ($request->type == 'find-password') {
                $userData = User::where('email', $request->id)
                    ->whereHas('contacts', function ($query) use ($request) {
                        $query->where('telephone_1', $request->phone)
                            ->orWhere('telephone_2', $request->phone);
                    })
                    ->where('first_name', $request->name)
                    ->withoutGlobalScopes()
                    ->first();

            } else if ($request->type == 'find-id') {
                $userData = User::whereHas('contacts', function ($query) use ($request) {
                        $query->where('telephone_1', $request->phone)
                            ->orWhere('telephone_2', $request->phone);
                    })
                    ->where('first_name', $request->name)
                    ->withoutGlobalScopes()
                    ->first();

            }

            // Get the phone number from the request
            $countryCode = $request->country_code;
            $phoneNumber = $request->phone;


            if ($userData) {
                // Generate a random 6-digit OTP
                $otp = mt_rand(100000, 999999);

                // Compose the message with the OTP using localization
                $message = __('messages.otp_message', ['otp' => $otp]);

                $fullNumber = $countryCode.$phoneNumber;

                // Send the OTP via SMS
                $this->sMSService->send($fullNumber, $message);

                // Store the OTP and user_id in the session using the service
                $this->sessionService->createSession([
                    'user_id' => $userData->id,
                    'key' => 'otp',
                    'value' => strval($otp),
                    'expires_at' => now()->addSeconds(130),
                    'is_used' => false,
                ]);

                return HelpersTraits::sendResponse([], __('messages.otp_sent_success'));
            } else {
                return HelpersTraits::sendError(__('messages.user_not_found'));
            }
        } catch (\Throwable $th) {
            return HelpersTraits::sendError( __('messages.unable_to_handle_request'),$th->getMessage());
        }
    }

    // /**
    //  * @OA\Post(
    //  *     path="/api/v1/auth/verify_otp",
    //  *     tags={"Authentication"},
    //  *     summary="Verify the OTP entered by the user",
    //  *     @OA\RequestBody(
    //  *         required=true,
    //  *         @OA\JsonContent(
    //  *             required={"user_id", "otp"},
    //  *             type="object",
    //  *             @OA\Property(property="user_id", type="integer", example=779),
    //  *             @OA\Property(property="otp", type="string", example="479499")
    //  *         )
    //  *     ),
    //  *     @OA\Response(
    //  *         response=200,
    //  *         description="OTP verified successfully",
    //  *         @OA\JsonContent(type="object")
    //  *     ),
    //  *     @OA\Response(
    //  *         response=401,
    //  *         description="Invalid OTP"
    //  *     )
    //  * )
    //  */
    private function verifyOTP(OTPRequest $request)
    {
        try {
            $otpSession = $this->sessionService->getSessionByKey('otp', $request->otp);

            if ($otpSession && $otpSession->user_id == $request->user_id && !$otpSession->is_used && $otpSession->expires_at > now()) {
                $this->sessionService->markOtpAsUsed($otpSession->value);

                return $this->sessionService->getSessionByKey('otp', $request->otp);
            } else {
                return false;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/verify_user",
     *     tags={"Authentication"},
     *     summary="Find user ID based on name and phone number",
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
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phone", "type", "id", "otp", "name"},
     *             type="object",
     *             @OA\Property(property="phone", type="string", example=1234567890),
     *             @OA\Property(property="type", type="string", example="find-password"),
     *             @OA\Property(property="id", type="string", example="developer@developer.com"),
     *             @OA\Property(property="otp", type="string", example="710690"),
     *             @OA\Property(property="name", type="string", example="user1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User ID found",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function verifyUser(VerifyUserRequest $request)
    {
        try {

            if ($request->type == 'find-password') {
                $userData = User::where('email', $request->id)
                    ->whereHas('contacts', function ($query) use ($request) {
                        $query->where('telephone_1', $request->phone);
                    })
                    ->where('first_name', $request->name)
                    ->withoutGlobalScopes()
                    ->first();

            } else if ($request->type == 'find-id') {
                $userData = User::whereHas('contacts', function ($query) use ($request) {
                    $query->where('telephone_1', $request->phone)
                        ->orWhere('telephone_2', $request->phone);
                })
                ->where('first_name', $request->name)
                ->withoutGlobalScopes()
                ->first();

            }

            // update the user OTP details if $user_id is not null
            if ($userData) {
                $otpRequest = new OTPRequest(['user_id' => $userData->id, 'otp' => $request->otp]);
                $otpSession = $this->verifyOTP($otpRequest);

                if ($otpSession && $otpSession->user_id == $userData->id && $otpSession->is_used && $otpSession->expires_at > now()) {
                    return HelpersTraits::sendResponse($userData, __('messages.user_retrieved'));
                }
                return HelpersTraits::sendError( __('messages.invalid_otp'));
            }
            return HelpersTraits::sendError( __('messages.user_not_found'));
        } catch (\Throwable $th) {
            return HelpersTraits::sendError(__('messages.unable_to_handle_request'));
        }
    }


    // /**
    //  * @OA\Post(
    //  *     path="/api/v1/auth/verify_phone",
    //  *     tags={"Authentication"},
    //  *     summary="Verify Phone",
    //  *     @OA\RequestBody(
    //  *         required=true,
    //  *         @OA\MediaType(
    //  *             mediaType="multipart/form-data",
    //  *             @OA\Schema(ref="#/components/schemas/VerifyPhoneRequest")
    //  *         )
    //  *     ),
    //  *     @OA\Response(
    //  *         response=200,
    //  *         description="User Verified",
    //  *         @OA\JsonContent(type="object")
    //  *     ),
    //  *     @OA\Response(
    //  *         response=400,
    //  *         description="User Not found"
    //  *     )
    //  * )
    //  */
    // public function verifyPhone(VerifyPhoneRequest $request)
    // {
    //     $data = $this->auth->checkContact(
    //         $request->name,
    //         $request->email,
    //         $request->phone,
    //         $request->has('type') ? $request->type : null,
    //     );

    //     if ($data['success'] === true) {
    //         return HelpersTraits::sendResponse($data['user'], $data['message']);
    //     }
    //     return HelpersTraits::sendError($data['message'], Response::HTTP_BAD_REQUEST);
    // }
}
