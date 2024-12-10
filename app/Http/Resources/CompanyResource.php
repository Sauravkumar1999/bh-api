<?php

    namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
            'name' => $this->name,
            'status' => $this->status,
            'url' => $this->url,
            'business_name' => $this->business_name,
            'representative_name' => $this->representative_name,
            'registration_number' => $this->registration_number,
            'address' => $this->address,
            'scope_of_disclosure' => $this->scope_of_disclosure,
            'logo' => $this->logo(),
            'contract' => $this->contract(),
            'registration_date' => $this->registration_date,
            'created_at' => $this->created_at,
        ];
    }
}
