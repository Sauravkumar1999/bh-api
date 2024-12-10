<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRequest;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use App\Traits\HelpersTraits;
use App\Models\Product;
use Symfony\Component\HttpFoundation\Response;

use Route;

class ProductController extends Controller
{

    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/products",
     *      tags={"Product"},
     *      summary="Get Product List",
     *      description="Get Product List",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Parameter(
     *          name="per_page",
     *          in="query",
     *          required=false,
     *          @OA\Schema(
     *              type="integer",
     *              default=10
     *          ),
     *          description="Number of Products per page"
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
     *       @OA\Parameter(
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
     *          description="Product List Found",
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
     *                  example="Product list found"
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
     *         description="Product List not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Product List not found"
     *             )
     *         )
     *      )
     * )
     */
    public function index(FilterRequest $request)
    {
        $filters = $request->filters();

        $products = Product::orderBy('id', 'DESC')
            ->filterAndPaginate($filters);

        if ($products->isEmpty()) {
            return HelpersTraits::sendError(null, __('messages.product_not_found'));
        }

        $product = ProductResource::collection($products);

        return HelpersTraits::sendResponse($product, __('messages.product_found'), $products);
    }


     /**
     * @OA\Post(
     *     path="/api/v1/products/create",
     *     summary="Create a new Product",
     *     security={{ "bearerAuth": {} }},
     *     tags={"Product"},
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
     *              encoding={
     *                  "approval_rights[]": {
     *                      "explode": true,
     *                  },
     *                  "company_id[]": {
     *                      "explode": true,
     *                  },
     *              },
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/ProductRequest")
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
    public function create(ProductRequest $request)
    {
        // dd($request->all());
        $data = $this->productService->createProduct($request);

        if ($data['success'] == true) {
            return HelpersTraits::sendResponse($data, __('messages.product_created'));
        }

        return HelpersTraits::sendError($data['message'], [], Response::HTTP_UNAUTHORIZED);
    }



    /**
     * @OA\Post(
     *     path="/api/v1/products/{id}/update",
     *     summary="Update an existing product",
     *     security={{ "bearerAuth": {} }},
     *     tags={"Product"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the product to update",
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
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            encoding={
     *                  "approval_rights[]": {
     *                      "explode": true,
     *                  },
     *                  "company_id[]": {
     *                      "explode": true,
     *                  },
     *             },
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/ProductRequest")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ProductRequest"
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
    public function update(ProductRequest $request, Product $id)
    {
        $data = $this->productService->updateProduct($request, $id);

        if ($data['success'] == true) {
            return HelpersTraits::sendResponse($data, __('messages.product_updated'));
        }

        return HelpersTraits::sendError($data['message'], [], Response::HTTP_UNAUTHORIZED);
    }

/**
 * @OA\Delete(
 *     path="/api/v1/products/{id}/delete",
 *     summary="Delete a product",
 *     security={{ "bearerAuth": {} }},
 *     tags={"Product"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the product to delete",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
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
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Product deleted successfully")
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
    $data = $this->productService->deleteProduct($id);

    if ($data['success'] == true) {
        return HelpersTraits::sendResponse($data, __('messages.product_deleted'));
    }

    return HelpersTraits::sendError($data['message'], [], Response::HTTP_UNAUTHORIZED);
}
}
