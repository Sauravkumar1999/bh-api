<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\CompanyService;
use App\Http\Requests\CompanyRequest;
use App\Traits\HelpersTraits;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Company as EntitiesCompany;
use App\Http\Requests\FilterRequest;
use App\Http\Resources\CompanyResource;

class CompanyController extends Controller
{
    private $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    /**
     * Index all companies in storage
     *
     *  @param  \App\Http\Requests\FilterRequest  $request
     *  @return \Illuminate\Http\JsonResponse
     *
     * @OA\GET(
     *     path="/api/v1/companies",
     *     tags={"Companies"},
     *     summary="List all available company in storage",
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
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Exception thrown/Validation error",
     *         @OA\JsonContent(type="object")
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function index(FilterRequest $request)
    {
        try {
            $filters = $request->filters();
            $companies = EntitiesCompany::orderBy('id', 'DESC')->filterAndPaginate($filters);
            if ($companies->isEmpty()) {
                return HelpersTraits::sendResponse(null, __('message.not_found'), null);
            }

            $company = CompanyResource::collection($companies);
            return HelpersTraits::sendResponse($company, __('message.found'), $companies);
        } catch (\Exception $e) {
            return HelpersTraits::sendError($e->getMessage());
        }
    }

    /**
     * Create a new company in storage.
     *
     * @param  \App\Http\Requests\CompanyRequest  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\POST(
     *     path="/api/v1/companies/create",
     *     tags={"Companies"},
     *     summary="Create a new company in storage",
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
     *         description="Company creation payload",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Company name"),
     *             @OA\Property(property="url", type="string", example="Company website Ex. example.com"),
     *             @OA\Property(property="business_name", type="string", example="Name of business XYZ Organization"),
     *             @OA\Property(property="representative_name", type="string", example="Company contact name"),
     *             @OA\Property(property="address", type="string", example="Company contact physical address"),
     *             @OA\Property(property="scope_of_disclosure", type="string", example="Disclosure policies"),
     *             @OA\Property(property="registration_date", type="string", example="2023-01-11 14:09:37"),
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Exception thrown/Validation error",
     *         @OA\JsonContent(type="object")
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function create(CompanyRequest $request)
    {
        $data = $this->companyService->registerCompany($request);
        if ($data['success'] === true) {
            return HelpersTraits::sendResponse($data['data'], __($data['message']));
        }
        return HelpersTraits::sendError(
            $data['message'],
            [],
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * Update a company in storage.
     * @param string $code
     * @param  \App\Http\Requests\CompanyRequest  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\PUT(
     *     path="/api/v1/companies/{code}/update",
     *     tags={"Companies"},
     *     summary="Update a new company in storage",
     *     @OA\Parameter(
     *         name="code",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
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
     *         description="Update company payload",
     *         @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  @OA\Property(property="name", type="string", example="Company name"),
     *                  @OA\Property(property="url", type="string", example="Company website Ex. example.com"),
     *                  @OA\Property(property="business_name", type="string", example="Name of business XYZ Organization"),
     *                  @OA\Property(property="representative_name", type="string", example="Company contact name"),
     *                  @OA\Property(property="address", type="string", example="Company contact physical address"),
     *                  @OA\Property(property="scope_of_disclosure", type="string", example="Disclosure policies"),
     *                  @OA\Property(property="registration_date", type="string", example="2024-01-11 14:09:37"),
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid company_code",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Exception thrown/Validation error",
     *         @OA\JsonContent(type="object")
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function update(CompanyRequest $request, $code)
    {
        $data = $this->companyService->updateCompany($request, $code);
        if ($data['success'] === true) {
            return HelpersTraits::sendResponse($data['data'], __($data['message']));
        }
        return HelpersTraits::sendError(
            $data['message'],
            [],
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * View company from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\GET(
     *     path="/api/v1/companies/{id}",
     *     tags={"Companies"},
     *     summary="View company in storage",
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
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid faq id"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Exception thrown",
     *         @OA\JsonContent(type="object")
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function view($id)
    {
        $data = $this->companyService->viewCompany($id);
        if ($data['success'] === true) {
            return HelpersTraits::sendResponse($data['data'], __($data['message']));
        }
        return HelpersTraits::sendError(
            $data['message'],
            [],
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * Remove company from storage.
     *
     * @param  string  $code
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\DELETE(
     *     path="/api/v1/companies/{code}/delete",
     *     tags={"Companies"},
     *     summary="Delete company from storage",
     *     @OA\Parameter(
     *         name="code",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
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
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid company code"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Exception thrown",
     *         @OA\JsonContent(type="object")
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function delete($code)
    {
        $data = $this->companyService->deleteCompany($code);
        if ($data['success'] === true) {
            return HelpersTraits::sendResponse([], __($data['message']));
        }
        return HelpersTraits::sendError(
            $data['message'],
            [],
            Response::HTTP_BAD_REQUEST
        );
    }
}
