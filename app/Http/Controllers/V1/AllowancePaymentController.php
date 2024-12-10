<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\AllowancePaymentRequest;
use App\Http\Requests\FilterRequest;
use App\Services\AllowancePaymentService;
use App\Http\Controllers\Controller;


class AllowancePaymentController extends Controller
{
    protected $allowanceService;

    public function __construct(AllowancePaymentService $allowanceService)
    {
        $this->allowanceService = $allowanceService;
    }

    /**
     * Display a listing of the allowance payments.
     *
     * @OA\Get(
     *     path="/api/v1/allowance-payments",
     *     tags={"Allowance"},
     *     summary="Get a list of allowance payments",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
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
     *      ),
     *      @OA\Response(
     *         response=200,
     *         description="OTP sent successfully",
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
        $filters = $request->filters();
        return $this->allowanceService->getAllAllowancePayments($filters);
    }



    /**
     * Store a newly created allowance payment in storage.
     *
     * @param  \App\Http\Requests\AllowancePaymentRequest  $request
     *
     * @OA\Post(
     *     path="/api/v1/allowance-payments/create",
     *     tags={"Allowance"},
     *     summary="Store a new allowance payment",
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
     *         description="Allowance payment data",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"title", "detail"},
     *                 @OA\Property(
     *                     property="title",
     *                     type="string",
     *                     example="Sample Allowance Payment"
     *                 ),
     *                 @OA\Property(
     *                     property="detail",
     *                     type="string",
     *                     example="Details of the allowance payment"
     *                 ),
     *                 @OA\Property(
     *                     property="attachment",
     *                     type="string",
     *                     format="binary",
     *                     example="file"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful operation",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(type="object")
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function store(AllowancePaymentRequest $request)
    {
        return $this->allowanceService->createAllowancePayment($request);
    }


    /**
     * Update the specified allowance payment in storage.
     *
     * @param  \App\Http\Requests\AllowancePaymentRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Patch(
     *     path="/api/v1/allowance-payments/{id}/update",
     *     tags={"Allowance"},
     *     summary="Update an allowance payment",
     *     @OA\Parameter(
     *         name="id",
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
     *         description="Updated allowance payment data",
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Sample Allowance Payment"),
     *             @OA\Property(property="detail", type="string", example="Details of the allowance payment"),
     *             @OA\Property(property="existing_attachment", type="string", example="1d2dc710c044186f2b4b647c51129f21-13"),
     *             @OA\Property(property="attchment", type="file", example="file")
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(type="object")
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function update(AllowancePaymentRequest $request, int $id)
    {
        return $this->allowanceService->updateAllowancePayment($request, $id);
    }

    /**
     * Remove the specified allowance payment from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Delete(
     *     path="/api/v1/allowance-payments/{id}/delete",
     *     tags={"Allowance"},
     *     summary="Delete an allowance payment",
     *     @OA\Parameter(
     *         name="id",
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
     *         response=204,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found",
     *         @OA\JsonContent(type="object")
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function delete($id)
    {
        return $this->allowanceService->deleteAllowancePayment($id);
    }


    /**
     * Display the specified allowance payment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/api/v1/allowance-payments/{id}/show",
     *     tags={"Allowance"},
     *     summary="Get a single allowance payment",
     *     @OA\Parameter(
     *         name="id",
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
     *                 example="Allowance payment retrieved successfully."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found",
     *         @OA\JsonContent(type="object")
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function show($id)
    {
        return $this->allowanceService->showSingleAllowancePayment($id);
    }
}
