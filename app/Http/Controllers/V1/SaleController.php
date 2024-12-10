<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRequest;
use App\Http\Requests\SaleRequest;
use App\Http\Resources\CustomSaleResource;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use App\Models\User;
use App\Models\Product;
use App\Models\Channel;
use App\Services\ChannelURLGeneratorService;
use App\Services\SaleService;
use App\Traits\HelpersTraits;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Response;

class SaleController extends Controller
{
    protected $urlGenerator;
    protected $SaleService;

    public function __construct(ChannelURLGeneratorService $urlGenerator, SaleService $SaleService)
    {
        $this->urlGenerator = $urlGenerator;
        $this->SaleService = $SaleService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/sales/my-page/",
     *     tags={"Sales"},
     *     summary="Get Product List",
     *     description="Get Product List based on user code",
     *     @OA\Parameter(
     *         name="user_code",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *         description="User code to fetch the products"
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=10
     *         ),
     *         description="Number of products per page"
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
     *         description="Product list found",
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
     *                 example="Product list found"
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
     *                 example="User not found"
     *             )
     *         )
     *     )
     * )
     */
    public function getChannels(FilterRequest $request)
    {
        try {
            $filters = $request->filters();

            if ($request->user_code) {
                $user = User::where('code', $request->user_code)->first();
                $userChannels = null;
                $mergedChannels = [];
                if ($user && $user->status === 1) {
                    if (isset($user->company)) {
                        $userChannels = $user->company->channelRights;

                        $mergedChannels = merge_user_settings($user, $userChannels);
                    }
                    // Convert merged channels to a standard Laravel collection
                    $mergedChannelsCollection = collect($mergedChannels);

                    // Get filters from the request or define default pagination settings
                    $perPage = $filters['per_page'] ?? config('erp.RECORDS_PER_PAGE');
                    $page = $request->page ?? 1;

                    // Paginate the merged channels collection
                    $paginatedChannels = $mergedChannelsCollection->forPage($page, $perPage);
                    $totalRecords = $mergedChannelsCollection->count();

                    // Create a LengthAwarePaginator instance
                    $paginator = new LengthAwarePaginator(
                        $paginatedChannels,
                        $totalRecords,
                        $perPage,
                        $page,
                        ['path' => Paginator::resolveCurrentPath()]
                    );

                    // Append the filters to pagination links
                    $paginator->appends($filters);
                    $channelList = SaleResource::collection($paginator->items());

                    return HelpersTraits::sendResponse($channelList, __('messages.channel_list_found'),$paginator);
                } else {
                    return HelpersTraits::sendError(__('messages.user_not_found'));
                }
            }
            $channels = Channel::orderBy('exposer_order', 'ASC')->filterAndPaginate($filters);
            $channelList = SaleResource::collection($channels);
            return HelpersTraits::sendResponse($channelList, __('messages.channel_list_found'), $channels);
        } catch (\Exception $e) {
            return HelpersTraits::sendError($e->getMessage());
        }
    }

    protected function addProductUrl($products, $user)
    {
        return $products->getCollection()->map(function ($product) use ($user) {
            if (!empty($product->url_params)) {

                $product->url_1 = $this->urlGenerator->createURL($product, $user);
            } else {

                $product->url_1 = isset($product->main_url) ? $product->main_url . '?bhid=' . $user->code : '';
            }

            return $product;
        });
    }

    /**
     * @OA\Get(
     *     path="/api/v1/sales",
     *     tags={"Sales"},
     *     summary="Get Sales List",
     *     description="Get Sales List ",
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=10
     *         ),
     *         description="Number of sales per page"
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
     *         description="sales list found",
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
     *                 example="Sale list found"
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
     *                 example="Sales not found"
     *             )
     *         )
     *     )
     * )
     */
    /**
     * @param FilterRequest $request
     * @return \Illuminate\Http\Response
     */
    public function getSales(FilterRequest $request)
    {
        try {

            $filters = $request->filters();
            $sales = Sale::with(['product.company', 'seller'])
                ->orderBy('id', 'DESC')
                ->filterAndPaginate($filters);
            if ($sales->isEmpty()) {
                return HelpersTraits::sendResponse(null, __('messages.not_found'), null);
            }
            return HelpersTraits::sendResponse(CustomSaleResource::collection($sales), __('messages.sale_found'), $sales);
        } catch (\Exception $e) {
            return HelpersTraits::sendError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/sales/create",
     *     summary="Create a new Sale",
     *     security={{ "bearerAuth": {} }},
     *     tags={"Sales"},
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
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(property="product_sale_day", type="string"),
     *                 @OA\Property(property="product_id", type="string"),
     *                 @OA\Property(property="company_id", type="string"),
     *                 @OA\Property(property="product_code", type="string"),
     *                 @OA\Property(property="fee_type", type="string"),
     *                 @OA\Property(property="product_price", type="string"),
     *                 @OA\Property(property="remark", type="string"),
     *                 @OA\Property(property="seller_id", type="string"),
     *                 @OA\Property(property="sales_price", type="string"),
     *                 @OA\Property(property="number_of_sales", type="string"),
     *                 @OA\Property(property="take", type="string"),
     *                 @OA\Property(property="sales_information", type="string"),
     *                 @OA\Property(property="product_sale_status", type="string"),
     *                 @OA\Property(property="user_id", type="string"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/SaleRequest"
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
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */

    /**
     * @param SaleRequest $request
     * @return \Illuminate\Http\Response
     */
    public function create(SaleRequest $request)
    {
        $data = $this->SaleService->createSale($request);

        if ($data['success'] == true) {
            return HelpersTraits::sendResponse($data, __('messages.sale_created'));
        }

        return HelpersTraits::sendError($data['message'], [], Response::HTTP_UNAUTHORIZED);
    }



    /**
     * @OA\Patch(
     *     path="/api/v1/sales/{id}/update",
     *     summary="Update an existing sale",
     *     security={{ "bearerAuth": {} }},
     *     tags={"Sales"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the sale to update",
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
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                   @OA\Property(property="product_sale_day", type="string"),
     *                  @OA\Property(property="product_id", type="string"),
     *                  @OA\Property(property="company_id", type="string"),
     *                  @OA\Property(property="product_code", type="string"),
     *                  @OA\Property(property="fee_type", type="string"),
     *                  @OA\Property(property="product_price", type="string"),
     *                  @OA\Property(property="remark", type="string"),
     *                  @OA\Property(property="seller_id", type="string"),
     *                  @OA\Property(property="sales_price", type="string"),
     *                  @OA\Property(property="number_of_sales", type="string"),
     *                  @OA\Property(property="take", type="string"),
     *                  @OA\Property(property="sales_information", type="string"),
     *                  @OA\Property(property="product_sale_status", type="string"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/SaleRequest"
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
     *         response=404,
     *         description="Sale not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    /**
     * @param SaleRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update(SaleRequest $request, $id)
    {
        try {
            $sale = Sale::find($id);
            if ($sale) {
                $data = $this->SaleService->updateSale($request, $id);
                if ($data['success']) {
                    $updatedData = Sale::findOrFail($id);
                    return HelpersTraits::sendResponse($updatedData, __('messages.sale_updated'));
                }
            } else {
                return HelpersTraits::sendError(__('messages.sale_not_found'), [], Response::HTTP_NOT_FOUND);
            }
            return HelpersTraits::sendError($data['message'], [], Response::HTTP_UNAUTHORIZED);
        } catch (ModelNotFoundException $e) {
            return HelpersTraits::sendError(__('messages.sale_not_found'), [], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return HelpersTraits::sendError($e->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/sales/{id}/delete",
     *     summary="Delete a sale",
     *     security={{ "bearerAuth": {} }},
     *     tags={"Sales"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the sale to delete",
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
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Sale deleted successfully")
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
     *         response=404,
     *         description="Sale not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */

    /**
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sale = Sale::find($id);
        if ($sale) {
            $data = $this->SaleService->deleteSale($id);
            if ($data['success'] == true) {
                return HelpersTraits::sendResponse($data, __('messages.sale_deleted'));
            }
        } else {
            return HelpersTraits::sendError(__('messages.sale_not_found'), [], Response::HTTP_NOT_FOUND);
        }

        return HelpersTraits::sendError($data['message'], [], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @OA\Get(
     * path="/api/v1/sales/{id}/detail",
     * summary="get Sale detail",
     * security={{ "bearerAuth": {} }},
     *  tags={"Sales"},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the sale to delete",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
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
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Sale deleted successfully")
     *          )
     *      ),
     * @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string")
     *          )
     *      ),
     * @OA\Response(
     *          response=404,
     *          description="Sale not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string")
     *          )
     *      )
     *  )
     * )
     */

    /**
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id)
    {
        $sale = Sale::find($id);
        if ($sale) {
            $data = Sale::with(['product.company', 'seller'])
                ->orderBy('id', 'DESC')
                ->where('id', $id)->get();
            if ($data->isEmpty()) {
                return HelpersTraits::sendResponse(null, __('messages.sale_not_found'), null);
            }
        } else {
            return HelpersTraits::sendError(__('messages.sale_not_found'), [], Response::HTTP_NOT_FOUND);
        }
        return HelpersTraits::sendResponse(CustomSaleResource::collection($data), __('messages.sale_found'));
    }


    /**
     * @OA\Get(
     * path="/api/v1/sales/{id}/product-detail",
     * summary="get Product detail",
     * security={{ "bearerAuth": {} }},
     *  tags={"Sales"},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the product to detail",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
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
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Product find successfully")
     *          )
     *      ),
     * @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string")
     *          )
     *      ),
     * @OA\Response(
     *          response=404,
     *          description="Product not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string")
     *          )
     *      )
     *  )
     * )
     */

    /**
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function productDetail($id)
    {
        $product = Product::where('id', $id)->first();
        if (empty($product)) {
            return HelpersTraits::sendResponse(null, __('messages.product_not_found'), null);
        }
        return HelpersTraits::sendResponse($product, __('messages.product_found'));
    }
}
