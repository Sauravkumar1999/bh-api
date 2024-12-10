<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="SaleRequest",
 *     type="object",
 *     required={"product_sale_day", "product_id", "company_id", "product_code", "fee_type", "product_price", "seller_id", "number_of_sales", "take", "sales_information","product_sale_status","user_id"},
 *                   @OA\Property(property="product_sale_day", type="string"),
 *                   @OA\Property(property="product_id", type="string"),
 *                   @OA\Property(property="company_id", type="string"),
 *                   @OA\Property(property="product_code", type="string"),
 *                   @OA\Property(property="fee_type", type="string"),
 *                   @OA\Property(property="product_price", type="string"),
 *                   @OA\Property(property="remark", type="string"),
 *                   @OA\Property(property="seller_id", type="string"),
 *                   @OA\Property(property="sales_price", type="string"),
 *                   @OA\Property(property="number_of_sales", type="string"),
 *                   @OA\Property(property="take", type="string"),
 *                   @OA\Property(property="sales_information", type="string"),
 *                   @OA\Property(property="product_sale_status", type="string"),
 *                   @OA\Property(property="user_id", type="string"),
 * )
 */
class SaleRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $customRule = 'nullable|integer';
        if ($this->routeIs('sales.create')) {
            $customRule = 'required|integer';
        }
        return [
            'product_sale_day'    => 'sometimes|required|date_format:Y-m-d H:i:s',
            'product_id'          => ['required', 'exists:products,id'],
            'company_id'          => 'required|integer',
            'fee_type'            => 'sometimes|required|in:fixed-price,with-ratio',
            'product_price'       => 'sometimes|required|numeric|between:0,9999999.99',
            'remark'              => 'nullable',
            'seller_id'           => ['required', 'exists:users,id'],
            'sales_price'         => 'nullable|numeric|between:0,9999999.99',
            'number_of_sales'     => 'required|integer',
            'sales_information'   => 'nullable',
            'product_sale_status' => 'nullable',
            'user_id' => $customRule,
        ];

    }
    public function messages()
    {
        return [
            'product_sale_day.required'  => __('validation.required', ['attribute' => __('validation.attributes.product_sale_day')]),
            'product_id.required' => __('validation.required', ['attribute' => __('validation.attributes.product_id')]),
            'company_id.required'     => __('validation.required', ['attribute' => __('validation.attributes.company_id')]),
            'fee_type.required'  => __('validation.required', ['attribute' => __('validation.attributes.fee_type')]),
            'product_price.required' => __('validation.required', ['attribute' => __('validation.attributes.product_price')]),
            'seller_id.required'     => __('validation.required', ['attribute' => __('validation.attributes.seller_id')]),
            'number_of_sales.required' => __('validation.required', ['attribute' => __('validation.attributes.number_of_sales')]),
        ];
    }

}
