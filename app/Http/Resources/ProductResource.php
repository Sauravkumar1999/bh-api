<?php

    namespace App\Http\Resources;

    use Illuminate\Http\Resources\Json\JsonResource;

    class ProductResource extends JsonResource
    {
        /**
         * Transform the resource into an array.
         *
         * @param  \Illuminate\Http\Request  $request
         *
         * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
         */
        public function toArray($request)
        {
            return [
                'id' => $this->id,
                'code' => $this->code,
                'product_name' => $this->product_name,
                'product_description' => $this->product_description,
                'product_price' => $this->product_price,
                'main_url' => $this->main_url,
                // 'url_params' => $this->url_params,
                // 'url_1' => $this->url_1,
                // 'url_2' => $this->url_2,
                'banner' => $this->banner(),
                // 'urls_open_mode' => $this->urls_open_mode,
                'sale_status' => $this->sale_status,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
                'company' => $this->company ? new ProductCompanyResource($this->company) : null,
            ];
        }
    }
