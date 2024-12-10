<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\FilterRequest;
use App\Services\AllowanceStatementService;
use App\Http\Controllers\Controller;


class AllowanceStatementController extends Controller
{
    protected $allowanceService;

    public function __construct(AllowanceStatementService $allowanceService)
    {
        $this->allowanceService = $allowanceService;
    }

    /**
     * Display a listing of the allowance statements.
     *
     * @OA\Get(
     *     path="/api/v1/allowance-statements",
     *     tags={"Allowance"},
     *     summary="Get a list of allowance statements",
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
     *         description="Allowance statement retrieved successfully.",
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
        return $this->allowanceService->getAllAllowanceStatements($filters);
    }

    /**
     * Display the specified allowance statements.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/api/v1/allowance-statements/get-allowance/{Month}",
     *     tags={"Allowance"},
     *     summary="Get a single allowance statements",
     *     @OA\Parameter(
     *         name="Month",
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
     *      ),
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
     *                 example="Allowance statement retrieved successfully."
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
    public function get_allowance($id)
    {
        return $this->allowanceService->showSingleAllowanceStatement($id);
    }
}
