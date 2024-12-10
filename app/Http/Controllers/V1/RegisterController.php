<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Services\RegisterService;
use App\Traits\HelpersTraits;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{

    protected $registerService;

    public function __construct(RegisterService $registerService)
    {
        $this->registerService = $registerService;
    }
    /**
     * @OA\Post(
     *     path="/api/v1/register",
     *     tags={"Authentication"},
     *     summary="Register a new user",
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
     *             @OA\Schema(ref="#/components/schemas/RegisterRequest")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful registration",
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
     *         description="Invalid credentials"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Content"
     *     )
     * )
     */
    public function __invoke(RegisterRequest $request)
    {
        $data = $this->registerService->registerUser($request);
        if($data['success'] === true){
            return HelpersTraits::sendResponse($data, __('messages.register_success'));
        }

        return HelpersTraits::sendError($data['message'], [],
            Response::HTTP_UNAUTHORIZED);
    }
}
