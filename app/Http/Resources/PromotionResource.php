<?php

    namespace App\Http\Resources;

use App\Models\Slider;
use Illuminate\Http\Resources\Json\JsonResource;

    class PromotionResource extends JsonResource
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
            if($this->promo_type == 'slider'){
                $slider = Slider::with('items')->where('id',$this->content)->limit(1)->get();
                $data['sliders'] = SliderResource::collection($slider);
                return $data;
            }

            return [
                'id' => $this->id,
                'promo_type' => $this->promo_type,
                'title' => $this->title,
                'content' => $this->content,
                'image' => $this->promotion_img(),
            ];
            
        }
    }
