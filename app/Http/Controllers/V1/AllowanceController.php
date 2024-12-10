<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Support\Renderable;
use App\Models\Allowance as EntitiesAllowance;
use App\Http\Requests\AllowanceRequest;
use App\Http\Requests\FilterRequest;
use App\Services\AllowanceService;
use App\Traits\HelpersTraits;
use App\Http\Resources\AllowanceResource;
use Symfony\Component\HttpFoundation\Response;


class AllowanceController extends Controller
{
    private $allowanceService;

    public function __construct(AllowanceService $allowanceService)
    {
        $this->allowanceService = $allowanceService;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     * Display a listing of the allowance payments.
     *
     * @OA\Get(
     *     path="/api/v1/allowances",
     *     tags={"allowances"},
     *     summary="Get a list of allowance payments",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
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
        $allowances = EntitiesAllowance::orderBy('id','DESC')->with('member')->filterAndPaginate($filters);

        if($allowances->isEmpty()){
            return HelpersTraits::sendError(__('messages.not_found'),[],
            Response::HTTP_BAD_REQUEST);
        }

        $allowance = AllowanceResource::collection($allowances);
        return HelpersTraits::sendResponse($allowance, __('messages.allowance_success_index'),$allowance);
    }

    /**
     * Display the specified allowance payment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/api/v1/allowances/{id}",
     *     tags={"allowances"},
     *     summary="Get a single allowance",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
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
     *                 example="Allowance retrieved successfully."
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

    public function view($id)
    {
        $data = $this->allowanceService->viewAllowance($id);
        if($data['success'] === true){
            return HelpersTraits::sendResponse($data['data'], __($data['message']));
        }
        return HelpersTraits::sendError($data['message'], [],
            Response::HTTP_BAD_REQUEST);
    }

    /**
     * Store a newly created allowance in storage.
     *
     * @param  \App\Http\Requests\AllowancePaymentRequest  $request
     *
     * @OA\Post(
     *     path="/api/v1/allowances/create",
     *     tags={"allowances"},
     *     summary="Store a new allowance",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Allowance data",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="payment_month",
     *                     type="string",
     *                     example="February"
     *                 ),
     *                 @OA\Property(
     *                     property="member_id",
     *                     type="string",
     *                     example="2"
     *                 ),
     *                 @OA\Property(
     *                     property="commission",
     *                     type="number",
     *                     example=45
     *                 ),
     *                 @OA\Property(
     *                     property="referral_bonus",
     *                     type="number",
     *                     example=30
     *                 ),
     *                 @OA\Property(
     *                     property="headquarters_representative_allowance",
     *                     type="number",
     *                     example=40
     *                 ),
     *                 @OA\Property(
     *                     property="organization_division_allowance",
     *                     type="number",
     *                     example=50
     *                 ),
     *                 @OA\Property(
     *                     property="policy_allowance",
     *                     type="number",
     *                     example=30
     *                 ),
     *                 @OA\Property(
     *                     property="other_allowances",
     *                     type="number",
     *                     example=30
     *                 ),
     *                 @OA\Property(
     *                     property="income_tax",
     *                     type="number",
     *                     example=20
     *                 ),
     *                 @OA\Property(
     *                     property="resident_tax",
     *                     type="number",
     *                     example=45
     *                 ),
     *                 @OA\Property(
     *                     property="year_end_settlement",
     *                     type="number",
     *                     example=30
     *                 ),
     *                 @OA\Property(
     *                     property="other_deductions_1",
     *                     type="number",
     *                     example=40
     *                 ),
     *                 @OA\Property(
     *                     property="other_deductions_2",
     *                     type="number",
     *                     example=50
     *                 ),
     *                 @OA\Property(
     *                     property="total_deduction",
     *                     type="number",
     *                     example=30
     *                 ),
     *                 @OA\Property(
     *                     property="total_before_tax",
     *                     type="number",
     *                     example=50
     *                 ),
     *                 @OA\Property(
     *                     property="deducted_amount_received",
     *                     type="number",
     *                     example=30
     *                 )
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

    public function create(AllowanceRequest $request)
    {
        $data  =  $this->allowanceService->createAllowance($request);
        if($data['success'] === true){
            return HelpersTraits::sendResponse($data['data'], __($data['message']));
        }
        return HelpersTraits::sendError($data['message'], [],
            Response::HTTP_BAD_REQUEST);
    }

    /**
     * Update the specified allowance payment in storage.
     *
     * @param  \App\Http\Requests\AllowancePaymentRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Patch(
     *     path="/api/v1/allowances/{id}/update",
     *     tags={"allowances"},
     *     summary="Update an allowance ",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated allowance  data",
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="payment_month",
     *                     type="string",
     *                     example="February"
     *                 ),
     *                 @OA\Property(
     *                     property="member_id",
     *                     type="string",
     *                     example="2"
     *                 ),
     *                 @OA\Property(
     *                     property="commission",
     *                     type="number",
     *                     example=45
     *                 ),
     *                 @OA\Property(
     *                     property="referral_bonus",
     *                     type="number",
     *                     example=30
     *                 ),
     *                 @OA\Property(
     *                     property="headquarters_representative_allowance",
     *                     type="number",
     *                     example=40
     *                 ),
     *                 @OA\Property(
     *                     property="organization_division_allowance",
     *                     type="number",
     *                     example=50
     *                 ),
     *                 @OA\Property(
     *                     property="policy_allowance",
     *                     type="number",
     *                     example=30
     *                 ),
     *                 @OA\Property(
     *                     property="other_allowances",
     *                     type="number",
     *                     example=30
     *                 ),
     *                 @OA\Property(
     *                     property="income_tax",
     *                     type="number",
     *                     example=20
     *                 ),
     *                 @OA\Property(
     *                     property="resident_tax",
     *                     type="number",
     *                     example=45
     *                 ),
     *                 @OA\Property(
     *                     property="year_end_settlement",
     *                     type="number",
     *                     example=30
     *                 ),
     *                 @OA\Property(
     *                     property="other_deductions_1",
     *                     type="number",
     *                     example=40
     *                 ),
     *                 @OA\Property(
     *                     property="other_deductions_2",
     *                     type="number",
     *                     example=50
     *                 ),
     *                 @OA\Property(
     *                     property="total_deduction",
     *                     type="number",
     *                     example=30
     *                 ),
     *                 @OA\Property(
     *                     property="total_before_tax",
     *                     type="number",
     *                     example=50
     *                 ),
     *                 @OA\Property(
     *                     property="deducted_amount_received",
     *                     type="number",
     *                     example=30
     *                 )
     *             )
     *         )
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

    public function update(AllowanceRequest $request, $id)
    {
        $data  =  $this->allowanceService->updateAllowance($request, $id);
        if($data['success'] === true){
            return HelpersTraits::sendResponse($data['data'], __($data['message']));
        }
        return HelpersTraits::sendError($data['message'], [],
            Response::HTTP_BAD_REQUEST);
    }

    /**
     * Remove the specified allowance payment from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Delete(
     *     path="/api/v1/allowances/{id}/delete",
     *     tags={"allowances"},
     *     summary="Delete an allowance",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
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
        $data = $this->allowanceService->deleteAllowance($id);
        if($data['success'] === true){
            return HelpersTraits::sendResponse([], __($data['message']));
        }
        return HelpersTraits::sendError($data['message'], [],
            Response::HTTP_BAD_REQUEST);
    }
}
