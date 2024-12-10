<?php

namespace App\Http\Resources;

use App\Models\Channel;
use Illuminate\Http\Resources\Json\JsonResource;

class MyInfoChannelsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this['id'],
            'channel_name' => $this['channel_name'],
            'odr_app' => $this['odr_app'],
            'channel_image' => Channel::find($this['id'])->mobileBanner(),
        ];
    }
}
