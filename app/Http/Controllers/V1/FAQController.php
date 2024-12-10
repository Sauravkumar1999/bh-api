<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\FAQService;
use App\Traits\HelpersTraits;
use App\Http\Requests\FAQRequest;
use App\Http\Requests\FilterRequest;
use App\Http\Resources\FAQResource;
use App\Models\FAQ as EntitiesFAQ;
use Symfony\Component\HttpFoundation\Response;

class FAQController extends Controller
{
    private $faqService;

    public function __construct(FAQService $faqService) {
        $this->faqService = $faqService;
    }

    /**
    * List all faqs in storage.
    * @param  \App\Http\Requests\FilterRequest  $request
    * @return \Illuminate\Http\JsonResponse
    *
    * @OA\GET(
    *     path="/api/v1/faq",
    *     tags={"faqs"},
    *     summary="List all available faqs in storage",
    *     @OA\Parameter(
    *          name="locale",
    *          in="query",
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
        $filters = $request->filters();
        $faqs = EntitiesFAQ::orderBy('id','DESC')->filterAndPaginate($filters);

        if($faqs->isEmpty()){
            return HelpersTraits::sendError(__('messages.not_found'),[],
            Response::HTTP_BAD_REQUEST);
        }

        $faq = FAQResource::collection($faqs);
        return HelpersTraits::sendResponse($faq,__('messages.faq_found'),$faqs);
    }

    /**
     * Create a new faq in storage.
     *
     * @param  \App\Http\Requests\FAQRequest  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\POST(
     *     path="/api/v1/faq/create",
     *     tags={"faqs"},
     *     summary="Create new FAQ in storage",
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
     *         description="FAQ creation payload",
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="FAQ title"),
     *             @OA\Property(property="description", type="string", example="FAQ"),
     *             @OA\Property(property="status", type="integer", example=1),
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
    public function create(FAQRequest $request)
    {
        $data = $this->faqService->createFAQ($request);
        if($data['success'] === true){
            return HelpersTraits::sendResponse($data['data'],$data['message']);
        }else{
            return HelpersTraits::sendError($data['message'],[],
            Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update faq in storage.
     *
     * @param  \App\Http\Requests\FAQRequest  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\PUT(
     *     path="/api/v1/faq/{id}/update",
     *     tags={"faqs"},
     *     summary="Update FAQ in storage",
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
     *         description="FAQ update payload",
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="FAQ title"),
     *             @OA\Property(property="description", type="string", example="FAQ"),
     *             @OA\Property(property="status", type="integer", example=1),
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
    public function update(FAQRequest $request,$id)
    {
        $data = $this->faqService->updateFAQ($request, $id);
        if($data['success'] === true){
            return HelpersTraits::sendResponse($data['data'],$data['message']);
        }else{
            return HelpersTraits::sendError($data['message'],[],
            Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * View faq from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\GET(
     *     path="/api/v1/faq/{id}",
     *     tags={"faqs"},
     *     summary="View faq in storage",
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
        $data = $this->faqService->viewFAQ($id);
        if($data['success'] === true){
            return HelpersTraits::sendResponse($data['data'],$data['message']);
        }else{
            return HelpersTraits::sendError($data['message'],[],
            Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Remove faq from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\DELETE(
     *     path="/api/v1/faq/{id}/delete",
     *     tags={"faqs"},
     *     summary="Delete faq from storage",
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
    public function delete($id)
    {
        $data = $this->faqService->deleteFAQ($id);
        if($data['success'] === true){
            return HelpersTraits::sendResponse($data['data'],$data['message']);
        }else{
            return HelpersTraits::sendError($data['message'],[],
            Response::HTTP_BAD_REQUEST);
        }
    }
}
