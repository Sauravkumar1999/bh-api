<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserSettingResource extends JsonResource
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
            // "id" => $this->resource['user_setting']->id ?? -1,
            "is_sale_person_image_enable" => $this->resource['user_setting']?->image_register ?? false,
            'sales_person_image' => $this->resource['sales_person_image'] ?? '',
            "is_bio_enable" => $this->resource['user_setting']?->text_register ?? false,
            "bio_text" => $this->resource['user_setting']?->text_registration ?? '',
            "email" => $this->resource['user_setting']?->email ?? '',
            "telephone" => $this->resource['user_setting']?->telephone ?? '',
            'telephone_formatted'=> $this->resource['user_setting']?->telephone ? format_phone_number($this->resource['user_setting']->telephone) : null,
            "portfolio" => $this->resource['user_setting']?->portfolio ?? '',
            "sns" => SnsResource::make($this->resource['user_setting']?->sns),
        ];
    }
}
