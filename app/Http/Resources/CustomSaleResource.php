<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Product\Entities\Product;


class CustomSaleResource extends JsonResource
{


    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'product_sale_day' => $this->product_sale_day,
            'product_id' => $this->product_id,
            'fee_type' => $this->fee_type,
            'product_price' => $this->product_price,
            'remark' => $this->remark,
            'sales_price' => $this->sales_price,
            'sales_type' => $this->sales_type,
            'take' => $this->take,
            'number_of_sales' => $this->number_of_sales,
            'sales_information' => $this->sales_information,
            'seller_id' => $this->seller_id,
            'operating_income' => $this->operating_income,
            'sales_status' => $this->sales_status,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'product' => new ProductResource($this->whenLoaded('product')), // Include product data
            'seller' => new UserResource($this->whenLoaded('seller')), // Include seller data

        ];
    }


}
