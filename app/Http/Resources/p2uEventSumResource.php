<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class p2uEventSumResource extends JsonResource
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
            'name' => auth()->user()->full_name,
            'sum' => $this[0] ?? 0
        ];
    }
}
