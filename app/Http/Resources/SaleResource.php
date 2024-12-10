<?php

namespace App\Http\Resources;

use App\Models\Channel;
use Illuminate\Http\Resources\Json\JsonResource;


class SaleResource extends JsonResource
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
            'id' => $this['id'],
            // 'code' => $this->code,
            'channel_name' => $this['channel_name'],
            'channel_description' => $this['channel_description'],
            'main_url' => $this['main_url'],
            // 'url_params' => $this->url_params,
            'url_1' => $this['url_1'],
            // 'url_2' => $this->url_2,
            'banner' => Channel::find($this['id'])->mobileBanner(),
            // 'sale_status' => $this->sale_status,
            // 'created_at' => $this->created_at,
            // 'updated_at' => $this->updated_at,
        ];
    }

}
