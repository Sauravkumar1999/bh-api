<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class P2UEventResource extends JsonResource
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
            'category'          => $this->event->name,
            'p2u_amount'        => $this->p2u_amount,
            'user_id'           => $this->user_id,
            'expire_date'       => $this->expires_at,
            'transfer_status'   => $this->transfer_status,
            'create_date'       => $this->created_at,
            'device_id'         => $this->device_id
        ];
    }

}
